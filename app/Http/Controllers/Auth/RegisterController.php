<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Address;
use App\Models\Store;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/rentals';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     */
    public function showRegistrationForm()
    {
        $addresses = Address::with(['city.country'])->get();
        $stores = Store::with(['address.city.country'])->get();
        
        return view('auth.register', compact('addresses', 'stores'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:45'],
            'last_name' => ['required', 'string', 'max:45'],
            'username' => ['required', 'string', 'max:16', 'unique:staff,username'],
            'email' => ['required', 'string', 'email', 'max:50', 'unique:staff,email'],
            'address_id' => ['required', 'exists:address,address_id'],
            'store_id' => ['required', 'exists:store,store_id'],
            'picture' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ], [
            'first_name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'El apellido es obligatorio.',
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.unique' => 'Este nombre de usuario ya está en uso.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ser un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'address_id.required' => 'Debe seleccionar una dirección.',
            'address_id.exists' => 'La dirección seleccionada no es válida.',
            'store_id.required' => 'Debe seleccionar una tienda.',
            'store_id.exists' => 'La tienda seleccionada no es válida.',
            'picture.image' => 'El archivo debe ser una imagen.',
            'picture.max' => 'La imagen no debe ser mayor a 2MB.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Generar contraseña temporal más corta (para campo VARCHAR(40))
        $temporaryPassword = Str::random(8);

        // Procesar imagen si se subió
        $pictureData = null;
        if (isset($data['picture']) && $data['picture']) {
            $pictureData = file_get_contents($data['picture']->getRealPath());
        }

        // Crear el usuario staff con hash más corto usando MD5
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'address_id' => $data['address_id'],
            'store_id' => $data['store_id'],
            'password' => md5($temporaryPassword), // Usar MD5 para que quepa en 40 chars
            'picture' => $pictureData,
            'active' => 1,
        ]);        // Enviar contraseña por correo
        $this->sendPasswordEmail($user, $temporaryPassword);

        return $user;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        // Procesar la imagen si existe
        $data = $request->all();
        if ($request->hasFile('picture')) {
            $data['picture'] = $request->file('picture');
        }

        event(new Registered($user = $this->create($data)));

        return redirect()->route('login')->with('success', 
            'Registro exitoso. Se ha enviado su contraseña temporal al correo: ' . $user->email);
    }

    /**
     * Enviar email con contraseña temporal
     */
    protected function sendPasswordEmail($user, $password)
    {
        try {
            Mail::send('emails.password', [
                'user' => $user,
                'password' => $password,
                'loginUrl' => route('login')
            ], function ($message) use ($user) {
                $message->to($user->email, $user->full_name)
                        ->subject('Bienvenido al Sistema Sakila - Credenciales de Acceso');
            });
        } catch (\Exception $e) {
            \Log::error('Error sending password email: ' . $e->getMessage());
        }
    }
}
