<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;

class SaleOfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */


     public function index(Request $request): View
     {
         $data = User::where('user_type', 2)
                     ->with('roles') // Eager load roles
                     ->latest()
                     ->paginate(5);
                    //  dd(User::with('roles')->get());
         return view('salesofficers.index', compact('data'))
             ->with('i', ($request->input('page', 1) - 1) * 5);
     }

    // public function index(Request $request): View
    // {
    //     $data = User::where('user_type', 2)->latest()->paginate(5);
    //     return view('salesofficers.index', compact('data'))
    //         ->with('i', ($request->input('page', 1) - 1) * 5);
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        $roles = Role::pluck('name', 'name')->all();
        return view('salesofficers.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'first_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['user_type'] = 2; 

        \Log::info('User type being set:', ['user_type' => $input['user_type']]);
       
        if ($request->hasFile('image')) {
            $fileName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads'), $fileName);
            $input['image'] = 'uploads/' . $fileName;
        }

        $user = User::create($input);

        return redirect()->route('salesofficers.index')
            ->with('success', 'Sales Officer created successfully');
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($encryptedId): View
    {
        try {
            // Decrypt the ID first
            $id = Crypt::decrypt($encryptedId);
            
            // Query using the decrypted numeric ID
            $user = User::where('id', $id)
                    ->where('user_type', 2)
                    ->firstOrFail();

                    // dd($user);
            return view('salesofficers.show', compact('user'));
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Invalid ID');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id): View
    {
        $user = User::where('id', $id)->where('user_type', 1)->firstOrFail();
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();

        return view('salesofficers.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, ['password']);
        }

        $user = User::where('id', $id)->where('user_type', 2)->firstOrFail();
        $user->update($input);

        // Remove existing roles and assign new ones
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->input('roles'));

        return redirect()->route('salesofficers.index')
            ->with('success', 'Sales Officer updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $user = User::where('id', $id)->where('user_type', 2)->firstOrFail();
        $user->delete();

        return redirect()->route('salesofficers.index')
            ->with('success', 'Sales Officer deleted successfully');
    }

    public function updateSellerActive(Request $request)
    {
        // Validate request data
        $request->validate([
            'id' => 'required|exists:users,id',
            'seller_active' => 'required|in:0,1,2'
        ]);

        // Find the user and update seller_active status
        $user = User::findOrFail($request->id);
        $user->seller_active = $request->seller_active;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Seller active status updated successfully.'
        ]);
    }

   
    

}
