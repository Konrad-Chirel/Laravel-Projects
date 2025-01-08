<x-layout>
    <h1 class="title">Welcome back</h1>
<div class="mx-auto max-w-screen-sm card">
    <form action="{{ route('login') }}" method="post">
        @csrf
        

        <div class="mb-4">
            <label for="email">Email</label>
            <input type="text" name="email"value="{{@old('username')}}" class="input @error('email') ring-red-500 @enderror">
           @error('email')

            <p class="error">{{ $message }} </p>
                
            @enderror
        </div>

        <div class="mb-4">
            <label for="password">Password</label>
            <input type="password" name="password" class="input">
            @error('password')

            <p class="error">{{ $message }} </p>
                
            @enderror
        </div>
        
        <div class="mb-4" >
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">Remember me</label>
        </div>

        @error('failed')

            <p class="error">{{ $message }} </p>
                
            @enderror
        <button class="bg-blue-500 text-white py-2 px-4 rounded">Login</button>
    </form>
</div>

</x-layout>
