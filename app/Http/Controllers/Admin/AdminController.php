class AdminController extends Controller
{
    public function create(Request $request)
    {
        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return back()->with('success', 'Admin created');
    }

    public function assignRole(Admin $admin, Request $request)
    {
        $admin->update(['role' => $request->role]);
        return back();
    }
}
