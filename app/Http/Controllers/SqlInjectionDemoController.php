<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SqlInjectionDemoController extends Controller
{
    /**
     * Show the SQL injection prevention examples page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('dashboard.sql-injection');
    }

    /**
     * Example of SQL injection vulnerability (DO NOT USE IN PRODUCTION)
     * Only for demonstration purposes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function vulnerable(Request $request)
    {
        $username = $request->input('username');

        // For demonstration purposes, show what would happen with SQL injection
        $rawQuery = "SELECT * FROM users WHERE name = '$username'";

        // Log the raw query for demonstration
        Log::info('Vulnerable query that would be executed: ' . $rawQuery);

        // SIMULATION of vulnerability for educational purposes
        // Instead of actually executing the vulnerable query, we'll demonstrate what would happen
        if (strpos($username, "'") !== false || strpos($username, '"') !== false ||
            strpos(strtolower($username), 'or') !== false || strpos(strtolower($username), 'and') !== false) {

            // This simulates what would happen if the SQL injection succeeded
            $results = User::all(); // Return all users as if the injection worked

            return view('dashboard.sql-injection', [
                'results' => $results,
                'query_type' => 'vulnerable - SQL INJECTION DETECTED!',
                'username' => $username,
                'rawQuery' => $rawQuery,
                'injectionDetected' => true
            ]);
        } else {
            // Normal query, no injection detected
            $results = User::where('name', $username)->get();

            return view('dashboard.sql-injection', [
                'results' => $results,
                'query_type' => 'vulnerable - no injection detected',
                'username' => $username,
                'rawQuery' => $rawQuery,
                'injectionDetected' => false
            ]);
        }
    }

    /**
     * Example of SQL injection prevention using query parameters
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function secureBind(Request $request)
    {
        $username = $request->input('username');

        // Safe query with parameter binding
        $rawQuery = "SELECT * FROM users WHERE name = ?";
        $results = DB::select($rawQuery, [$username]);

        return view('dashboard.sql-injection', [
            'results' => $results,
            'query_type' => 'secure_bind',
            'username' => $username,
            'rawQuery' => $rawQuery . " [Parameter: $username]",
            'injectionDetected' => false
        ]);
    }

    /**
     * Example of SQL injection prevention using Laravel's Query Builder
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function secureQueryBuilder(Request $request)
    {
        $username = $request->input('username');

        // Query builder automatically handles parameter binding
        $results = DB::table('users')
                    ->where('name', $username)
                    ->get();

        $rawQuery = "DB::table('users')->where('name', '$username')->get()";

        return view('dashboard.sql-injection', [
            'results' => $results,
            'query_type' => 'secure_builder',
            'username' => $username,
            'rawQuery' => $rawQuery,
            'injectionDetected' => false
        ]);
    }

    /**
     * Example of SQL injection prevention using Eloquent ORM
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function secureEloquent(Request $request)
    {
        $username = $request->input('username');

        // Eloquent ORM uses query builder under the hood
        $results = User::where('name', $username)->get();

        $rawQuery = "User::where('name', '$username')->get()";

        return view('dashboard.sql-injection', [
            'results' => $results,
            'query_type' => 'secure_eloquent',
            'username' => $username,
            'rawQuery' => $rawQuery,
            'injectionDetected' => false
        ]);
    }

    /**
     * Example of SQL injection prevention using request validation
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function secureValidation(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'username' => 'required|string|alpha_num|max:255',
            ]);

            $username = $validatedData['username'];

            // After validation, use one of the secure methods
            $results = User::where('name', $username)->get();

            $rawQuery = "User::where('name', '$username')->get() [after validation]";

            return view('dashboard.sql-injection', [
                'results' => $results,
                'query_type' => 'secure_validation',
                'username' => $username,
                'rawQuery' => $rawQuery,
                'injectionDetected' => false
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }
    }
}
