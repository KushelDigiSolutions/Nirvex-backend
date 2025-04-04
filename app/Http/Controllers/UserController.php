<?php
    
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;

    
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $data = User::latest()->paginate(5);
  
        return view('users.index',compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $roles = Role::pluck('name','name')->all();
        return view('users.create',compact('roles'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'first_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);
    
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
    
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
    
        return redirect()->route('users.index')
                        ->with('success','User created successfully');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        $user = User::find($id);
        return view('users.show',compact('user'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        // $userRole = $user->roles->pluck('first_name','first_name')->all();
        $userRole = $user->roles->pluck('name')->toArray();
    
        return view('users.edit',compact('user','roles','userRole'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);
    
        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }
    
        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();
    
        $user->assignRole($request->input('roles'));
    
        return redirect()->route('users.index')
                        ->with('success','User updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */



    public function getCustomer(){
        $data = User::role('customers')->get();
        return view('admin.customers.index',compact('data'));
    }

    public function getClient(){
        $data = User::withoutRole(['seller', 'customers'])->get();
        return view('admin.clients.index',compact('data'));
    }

    public function getCustomerById($id): View{

        $id = Crypt::decrypt($id);
        
        $user = User::with('addresses')->findOrFail($id);
        
        return view('admin.customers.show',compact('user'));
    }


    public function destroy($id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->delete();
    
        return redirect()->route('clients.index')->with('success', 'User deleted successfully.');
    }

    public function createCustomer(): View
    {
        return view('admin.clients.create');
    }
    



    public function storeStaff(Request $request)
    {
        // dd($request->all());
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'phone' => 'required|string|unique:users,phone',
        'email' => 'required|string|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'status' => ['required'],
        'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:1024',
    ]);

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $imagePath = public_path('uploads/profile');

        $image->move($imagePath, $imageName);
        $imageUrl = 'uploads/profile/' . $imageName;
    } else {
        return back()->withErrors(['image' => 'The image failed to upload.']);
    }
    $user = User::create([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'phone' => $request->phone,
        'email' => $request->email,
        'password' => $request->password,
        'image' => $imageUrl,
        'status' => $request->status,
        'user_type' => 1,
    ]);
  return redirect()->route('clients.index')->with('success', 'Staff Admin successfully created.');
}


}