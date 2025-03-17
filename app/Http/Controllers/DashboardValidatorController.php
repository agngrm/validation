<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DashboardValidatorController extends Controller
{
    /**
     * Show the dashboard with validator form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('dashboard.validator');
    }

    /**
     * Process the validation example.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'price' => 'required|numeric|min:1000|max:1000000',
            'sale_price' => 'nullable|numeric|lt:price',
            'age' => 'required|integer|between:18,100',
            'website' => 'nullable|url',
            'date_of_birth' => 'required|date|before:today',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required|accepted',
        ], [
            'price.required' => 'Harga produk wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal Rp 1.000',
            'price.max' => 'Harga maksimal Rp 1.000.000',
            'sale_price.numeric' => 'Harga diskon harus berupa angka',
            'sale_price.lt' => 'Harga diskon harus lebih kecil dari harga normal',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()
                ->route('dashboard.validator')
                ->withErrors($validator)
                ->withInput();
        }

        // Process the valid data
        $validatedData = $validator->validated();

        // Calculate discount percentage if sale price is provided
        if (!empty($validatedData['sale_price']) && $validatedData['price'] > 0) {
            $discount = (($validatedData['price'] - $validatedData['sale_price']) / $validatedData['price']) * 100;
            $discountFormatted = number_format($discount, 2) . '%';
        } else {
            $discountFormatted = 'N/A';
        }

        // Here you would typically do something with the data
        // For demonstration purposes, we'll just redirect with success

        return redirect()
            ->route('dashboard.validator')
            ->with('success', 'Validation passed successfully! Price: Rp ' . number_format($validatedData['price']) .
                  ', Sale Price: ' . (isset($validatedData['sale_price']) ? 'Rp ' . number_format($validatedData['sale_price']) : 'None') .
                  ', Discount: ' . $discountFormatted);
    }
}
