<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('SQL Injection Prevention') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                        <p class="text-red-700"><strong>Examples of vulnerable inputs:</strong></p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li><code>' OR '1'='1</code> - Returns all users</li>
                            <li><code>' OR 1=1--</code> - Returns all users with comment</li>
                            <li><code>' UNION SELECT * FROM users--</code> - Union attack</li>
                        </ul>
                        <p class="text-red-700 mt-2">For demo purposes, try entering: <code>' OR '1'='1</code></p>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4">Try Different Methods</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-medium mb-2">Vulnerable Method (DO NOT USE IN PRODUCTION)</h4>
                                <form action="{{ route('sql.vulnerable') }}" method="POST" class="mb-4">
                                    @csrf
                                    <div class="flex">
                                        <input type="text" name="username" placeholder="Enter username" class="flex-1 rounded-l border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-r">Search (Unsafe)</button>
                                    </div>
                                </form>
                            </div>

                            <div>
                                <h4 class="font-medium mb-2">Secure Method 1: Parameter Binding</h4>
                                <form action="{{ route('sql.secure.bind') }}" method="POST" class="mb-4">
                                    @csrf
                                    <div class="flex">
                                        <input type="text" name="username" placeholder="Enter username" class="flex-1 rounded-l border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-r">Search (Safe)</button>
                                    </div>
                                </form>
                            </div>

                            <div>
                                <h4 class="font-medium mb-2">Secure Method 2: Query Builder</h4>
                                <form action="{{ route('sql.secure.builder') }}" method="POST" class="mb-4">
                                    @csrf
                                    <div class="flex">
                                        <input type="text" name="username" placeholder="Enter username" class="flex-1 rounded-l border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-r">Search (Safe)</button>
                                    </div>
                                </form>
                            </div>

                            <div>
                                <h4 class="font-medium mb-2">Secure Method 3: Eloquent ORM</h4>
                                <form action="{{ route('sql.secure.eloquent') }}" method="POST" class="mb-4">
                                    @csrf
                                    <div class="flex">
                                        <input type="text" name="username" placeholder="Enter username" class="flex-1 rounded-l border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-r">Search (Safe)</button>
                                    </div>
                                </form>
                            </div>

                            <div>
                                <h4 class="font-medium mb-2">Secure Method 4: Request Validation</h4>
                                <form action="{{ route('sql.secure.validation') }}" method="POST" class="mb-4">
                                    @csrf
                                    <div class="flex">
                                        <input type="text" name="username" placeholder="Enter username" class="flex-1 rounded-l border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-r">Search (Safe)</button>
                                    </div>
                                </form>
                                @error('username')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    @if(isset($results))
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold mb-4">Results ({{ $query_type }})</h3>
                            <p class="mb-2">You searched for: <code>{{ $username }}</code></p>

                            @if(isset($rawQuery))
                                <div class="bg-gray-100 p-4 rounded mb-4">
                                    <p class="font-medium">Query that would be executed:</p>
                                    <pre class="mt-2 whitespace-pre-wrap text-sm">{{ $rawQuery }}</pre>
                                </div>
                            @endif

                            @if(isset($injectionDetected) && $injectionDetected)
                                <div class="bg-red-100 border-l-4 border-red-500 p-4 mb-4">
                                    <p class="text-red-700 font-medium">SQL Injection Detected!</p>
                                    <p class="text-red-700 mt-1">In a vulnerable application, this would have exposed all users from the database.</p>
                                </div>
                            @endif

                            @if(count($results) > 0)
                                <div class="bg-white shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($results as $user)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->id }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-gray-500">No results found.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
