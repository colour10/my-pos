<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Bank;
use App\Model\Role;
use App\Model\Permission;
use App\Model\Manager;
use App\Model\Account;
use App\Model\Card;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SystemController extends Controller
{
    // 顶级权限
    protected $parent_permissions;
    // 当前控制器/方法
    protected $controller_action;

    // 构造函数
    public function __construct()
    {
        $this->parent_permissions = Permission::select(['id', 'description'])->where('pid', 0)->get();
        // 控制器
        $this->controller_action = $this->getControllerAction();
    }

    // 开户行列表
    public function bankindex(Request $request)
    {
        // 逻辑，判断是否有搜索关键词
        $name = request('name');

        // 渲染
        $page_title = '开户行列表';
        $banks = Bank::select(['id', 'name', 'created_at', 'updated_at'])->orderBy('created_at', 'asc')->where('name', 'like', '%' . $name . '%')->paginate(10);
        return view('admin.system.bank.index', compact('page_title', 'banks', 'name'));
    }

    // 开户行查看
    public function bankshow(Request $request, $id)
    {
        // 渲染
        $page_title = '开户行查看';
        $bank = Bank::find($id);
        return view('admin.system.bank.show', compact('page_title', 'bank'));
    }

    // 开户行增加
    public function bankcreate()
    {
        $page_title = '开户行增加';
        return view('admin.system.bank.create', compact('page_title'));
    }

    // 开户行增加逻辑
    public function bankstore(Request $request)
    {
        // 验证
        $this->validate($request, [
            'name' => 'required|string|unique:banks,name',
        ]);

        // 逻辑
        $name = request('name');
        if (Bank::create(compact('name'))) {
            $data = [
                'code' => '0',
                'msg' => '开户行添加成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '开户行添加失败',
            ];
        }
        // 返回
        return $data;
    }

    // 开户行编辑
    public function bankedit($id)
    {
        // 渲染
        $page_title = '开户行编辑';
        $model = Bank::select(['id', 'name', 'created_at', 'updated_at'])->find($id);
        if ($model) {
            return view('admin.system.bank.edit', compact('page_title', 'model'));
        } else {
            return view('errors.404');
        }
    }

    // 开户行编辑逻辑
    public function bankupdate(Request $request, $id)
    {
        // 验证
        $this->validate($request, [
            'name' => 'required|string|unique:banks,name,' . $id,
        ]);

        // 逻辑
        $name = request('name');
        if (Bank::find($id)->update(compact('name'))) {
            $data = [
                'code' => '0',
                'msg' => '开户行修改成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '开户行修改失败',
            ];
        }
        // 返回
        return $data;
    }

    // 开户行删除
    public function bankdestroy($id)
    {
        // 首先判断是否有卡号在使用着
        $cards = Card::where('bank_id', $id)->get();
        if ($cards->count() > 0) {
            $data = [
                'code' => '1',
                'msg' => '此银行是部分合伙人账户的所属银行，所以不能删除！',
            ];
        } else {
            // 如果没有被占用，那么就执行删除
            $result = Bank::destroy($id);
            if ($result == '1') {
                $data = [
                    'code' => '0',
                    'msg' => '删除成功',
                ];
            } else {
                $data = [
                    'code' => '1',
                    'msg' => '删除失败',
                ];
            }
        }
        // 返回
        return $data;
    }




    // 角色功能
    // 创建角色页面
    public function rolecreate()
    {
        $page_title = '创建角色';
        return view('admin.system.role.create', compact('page_title'));
    }

    // 创建角色页面逻辑
    public function rolestore(Request $request)
    {
        // 验证
        $this->validate($request, [
            'name' => 'required|min:3',
            'description' => 'required',
        ]);

        // 逻辑
        if (Role::create(request(['name', 'description']))) {
            $data = [
                'code' => '0',
                'msg' => '角色添加成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '角色添加失败',
            ];
        }
        // 返回
        return $data;
    }

    // 角色列表
    public function roleindex(Request $request)
    {
        // 渲染
        $name = request('name');
        $page_title = '角色管理';
        $roles = Role::orderBy('created_at', 'asc')->where('name', 'like', '%' . $name . '%')->paginate(10);
        return view('admin.system.role.index', compact('page_title', 'roles', 'name'));
    }

    // 角色分配权限页面
    public function rolepermission(Request $request, $id)
    {
        // 标题
        $page_title = '角色分配权限';

        // 把权限生成分类树
        // 首先取出所有权限
        $result = Permission::select(['id', 'pid', 'description'])->get();

        // 格式化结果集
        $permissions = $this->create_tree($result);

        // 一级权限
        $first_permissions = Permission::select(['id', 'pid', 'description'])->where('pid', 0)->get();

        // 二级权限
        $second_permissions = Permission::select(['id', 'pid', 'description'])->where('pid', '<>', 0)->get();

        // 取出当前管理员拥有的权限
        $myPermissions = Role::find($id)->permissions;

        // 渲染
        return view('admin.system.role.permission', compact('page_title', 'permissions', 'myPermissions', 'first_permissions', 'second_permissions', 'id'));

    }

    // 角色分配权限逻辑
    public function roleassignPermission(Request $request, $id)
    {
        // 验证
        $this->validate($request, [
            'permissions' => 'required|array',
        ]);

        // 逻辑
        // 首先将该角色的权限全部清空
        $permissions = Permission::find(request('permissions'));
        $role = Role::find($id);
        $myPermissions = $role->permissions;

        // 对没有的权限,添加
        $addPermissions = $permissions->diff($myPermissions);
        foreach ($addPermissions as $permission) {
            $role->grantPermission($permission);
        }
        // 以前单方面有的删除
        $deletePermissions = $myPermissions->diff($permissions);
        foreach ($deletePermissions as $permission) {
            $role->deletePermission($permission);
        }

        // 返回
        $data = [
            'code' => '0',
            'msg' => '角色分配权限成功',
        ];
        return $data;

    }

    // 角色编辑页面
    public function roleedit(Request $request, $id)
    {
        $page_title = '角色编辑';
        $role = Role::find($id);
        return view('admin.system.role.edit', compact('page_title', 'role'));
    }

    // 角色编辑逻辑
    public function roleupdate(Request $request, $id)
    {
        // 验证
        $this->validate($request, [
            'name' => 'required|min:3|unique:roles,name,' . $id,
            'description' => 'required',
        ]);

        // 逻辑
        $role = Role::find($id);
        if ($role->update(request(['name', 'description']))) {
            $data = [
                'code' => '0',
                'msg' => '角色编辑成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '角色编辑失败',
            ];
        }
        // 返回
        return $data;
    }

    // 删除角色
    public function roledestroy(Request $request, $id)
    {
        // 逻辑
        $result = Role::destroy($id);

        if ($result == '1') {
            $data = [
                'code' => '0',
                'msg' => '删除成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '删除失败',
            ];
        }
        // 返回
        return $data;
    }




    // 权限设置功能
    // 创建权限页面
    public function permissioncreate()
    {
        // 渲染
        $page_title = '创建权限';
        $parent_permissions = $this->parent_permissions;
        return view('admin.system.permission.create', compact('page_title', 'parent_permissions'));
    }

    // 创建权限逻辑
    public function permissionstore(Request $request)
    {
        // 验证
        $this->validate($request, [
            'name' => 'required|min:3|unique:permissions,name',
            'description' => 'required',
        ]);

        // 逻辑
        if (Permission::create(request(['pid', 'name', 'description']))) {
            $data = [
                'code' => '0',
                'msg' => '权限创建成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '权限创建失败',
            ];
        }
        // 返回
        return $data;

    }

    // 权限管理
    public function permissionindex(Request $request)
    {
        // 渲染
        $name = request('name');
        $page_title = '权限管理';
        $permissions = Permission::select(['id', 'pid', 'name', 'description', 'created_at', 'updated_at'])->orderBy('created_at', 'asc')->where('name', 'like', '%' . $name . '%')->paginate(10);
        return view('admin.system.permission.index', compact('page_title', 'permissions', 'name'));
    }

    // 权限修改
    public function permissionedit(Request $request, $id)
    {
        // 渲染
        $page_title = '权限修改';
        $parent_permissions = $this->parent_permissions;
        $permission = Permission::find($id);
        return view('admin.system.permission.edit', compact('page_title', 'permission', 'parent_permissions'));
    }

    // 权限修改逻辑
    public function permissionupdate(Request $request, $id)
    {
        // 验证
        $this->validate($request, [
            'name' => 'required|min:3|unique:permissions,name,' . $id,
            'description' => 'required',
        ]);

        // 逻辑
        $permission = Permission::find($id);
        if ($permission->update(request(['pid', 'name', 'description']))) {
            $data = [
                'code' => '0',
                'msg' => '权限修改成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '权限修改失败',
            ];
        }
        // 返回
        return $data;

    }

    // 权限删除
    public function permissiondestroy(Request $request, $id)
    {
        // 逻辑
        $result = Permission::destroy($id);

        if ($result == '1') {
            $data = [
                'code' => '0',
                'msg' => '删除成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '删除失败',
            ];
        }
        // 返回
        return $data;
    }

    /**
     * 将结果集格式化为目录树，这个暂时没用到
     * @param $result resource 结果集
     * @param $pid int 父类id
     * @param $level int 当前分类等级
     * @return array
     */
    private function create_tree($result, $pid = 0, $level = 0)
    {
        // 初始化一个空数组和一个字符串
        $children = [];
        $str = '|----';
        foreach ($result as $k => $v) {
            if ($v['pid'] == $pid) {
                $children[] = [
                    'id' => $v['id'],
                    'pid' => $v['pid'],
                    'description' => $v['description'],
                    'html' => str_repeat($str, $level),
                ];
                // 马上递归去找当前栏目的下一级菜单
                $children = array_merge($children, $this->create_tree($result, $v['id'], $level + 1));
            }
        }
        // 返回
        return $children;
    }


    // 管理员管理
    // 添加管理员
    public function managercreate()
    {
        $page_title = '添加管理员';
        return view('admin.system.manager.create', compact('page_title'));
    }

    // 添加管理员逻辑
    public function managerstore(Request $request)
    {
        // 验证
        $this->validate($request, [
            'name' => 'required|min:2',
            'mobile' => 'required|unique:managers,mobile|regex:/^1[345678][0-9]{9}$/',
            'password' => 'required',
            'email' => 'required|email|unique:managers,email',
            'status' => 'required|integer',
        ]);

        // 逻辑
        $name = request('name');
        $mobile = request('mobile');
        $password = bcrypt(request('password'));
        $email = request('email');
        $status = request('status');
        $creater = \Session::get('admin')['admin_id'];

        $id = Manager::create(compact('name', 'mobile', 'password', 'email', 'status', 'creater'))->id;

        // 默认给新增加的用户添加一个普通管理员的权限
        // $role = Role::find(2);
        // Manager::find($id)->assignRole($role);

        // 判断
        if ($id) {
            $data = [
                'code' => '0',
                'msg' => '恭喜您，管理员添加成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '很遗憾，管理员添加失败',
            ];
        }
        // 返回
        return $data;
    }

    // 修改管理员
    public function manageredit(Request $request, $id)
    {
        $page_title = '修改管理员';
        $manager = Manager::find($id);
        return view('admin.system.manager.edit', compact('page_title', 'manager'));
    }

    // 修改管理员逻辑
    public function managerupdate(Request $request, $id)
    {
        // 验证
        $this->validate(request(), [
            'name' => 'required|min:2',
            'mobile' => 'required|unique:managers,mobile,' . $id . '|regex:/^1[345678][0-9]{9}$/',
            'email' => 'required|email|unique:managers,email,' . $id,
            'status' => 'required|integer',
        ]);

        // 逻辑
        $manager = Manager::find($id);
        $name = request('name');
        $mobile = request('mobile');
        $password = empty(request('password')) ? $manager->password : bcrypt(request('password'));
        $email = request('email');
        $status = request('status');
        $result = $manager->update(compact('name', 'mobile', 'password', 'email', 'status'));

        // 判断结果
        if ($result) {
            $data = [
                'code' => '0',
                'msg' => '恭喜您，管理员修改成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '很遗憾，管理员修改失败',
            ];
        }
        // 返回
        return $data;
    }

    // 删除管理员
    public function managerdestroy(Request $request, $id)
    {
        // 获得当前用户的角色
        $managerModel = Manager::find($id);
        $roles = $managerModel->roles;

        // 不能删除自己的账号
        $session = $request->session();
        $admin_id = \Session::get('admin')['admin_id'];
        if ($admin_id == $id) {
            // 渲染
            return redirect()->route('ManagerIndex')->with('error', '错误！！您不能删除自己的账号！！');
        }
        // 删除管理员
        $manager = $managerModel->delete();
        // 渲染
        return redirect()->route('ManagerIndex')->with('success', '管理员删除成功');
    }

    // 管理员列表
    public function managerindex(Request $request)
    {
        // 取数据
        $page_title = '管理员列表';
        $controller_action = $this->controller_action;
        $managers = Manager::select(['id', 'name', 'mobile', 'email', 'creater', 'last_login_at', 'status', 'created_at'])->orderBy('id', 'asc')->paginate(10);

        // 对数据进行处理
        foreach ($managers as $k => $manager) {
            // 创建者的名字
            $managers[$k]->creater_name = Manager::find($manager->creater)->name;
            switch ($manager->status) {
                case '0':
                    $managers[$k]->status_name = '禁用';
                    break;
                case '1':
                    $managers[$k]->status_name = '正常';
                    break;
            }
        }

        // 渲染
        return view('admin.system.manager.index', compact('page_title', 'managers', 'request', 'controller_action'));
    }

    // 搜索
    public function managersearch(Request $request)
    {
        // DB类搜索逻辑
        $managers = DB::table('managers')
            ->select(['id', 'name', 'mobile', 'email', 'creater', 'last_login_at', 'status', 'created_at'])
            ->where(function ($query) use ($request) {
                $name = $request->input('name');
                if (!empty($name)) {
                    $query->where('name', 'like binary', '%' . $name . '%');
                }
            })
            ->where(function ($query) use ($request) {
                $mobile = $request->input('mobile');
                if (!empty($mobile)) {
                    $query->where('mobile', $mobile);
                }
            })
            ->where(function ($query) use ($request) {
                $status = $request->get('status');
                if (isset($status)) {
                    $query->where('status', $status);
                }
            })
            ->orderBy('id', 'asc')
            ->paginate(10);

        // 这里对数据进行一下处理
        foreach ($managers as $k => $manager) {
            // 创建者的名字
            $managers[$k]->creater_name = Manager::find($manager->id)->name;
            switch ($manager->status) {
                case '0':
                    $managers[$k]->status_name = '禁用';
                    break;
                case '1':
                    $managers[$k]->status_name = '正常';
                    break;
            }
        }

        // 渲染
        $page_title = '搜索结果';
        $controller_action = $this->controller_action;

        return view('admin.system.manager.index', compact('page_title', 'managers', 'request', 'controller_action'));
    }

    // 获取管理员现有的角色
    public function managerrole(Request $request, $id)
    {
        // 标题
        $page_title = '管理员分配角色';

        // 找出模型
        $manager = Manager::find($id);

        // 查找角色
        $myRoles = $manager->roles;

        // 找出所有角色
        $roles = Role::get();

        // 渲染
        return view('admin.system.manager.role', compact('myRoles', 'roles', 'page_title', 'id'));
    }

    // 管理员分配角色
    public function managerassignRole(Request $request, $id)
    {
        // 验证
        $this->validate(request(), [
            'roles' => 'required|array',
        ]);

        // 逻辑
        DB::beginTransaction();
        try {

            // 拿到传过来的角色值
            $roles = Role::findMany(request('roles'));
            if (!$roles->count()) {
                throw new \Exception('角色不存在，请联系网站管理员进行处理！');
            }
            // 取出现在的用户拥有的角色列表，需要用到关联，才能拿到collection
            $manager = Manager::find($id);
            if (!$manager->count()) {
                throw new \Exception('严重错误，当前管理员不存在！');
            }

            $myRoles = $manager->roles;
            // 找差集，如果存在就增加
            $addRoles = $roles->diff($myRoles);

            foreach ($addRoles as $addRole) {
                $manager->assignRole($addRole);
            }

            // 反过来，把原来存在的，但是传入值不存在的给与删除
            $deleteRoles = $myRoles->diff($roles);
            foreach ($deleteRoles as $deleteRole) {
                $manager->deleteRole($deleteRole);
            }

            // 提交
            DB::commit();
            $data = [
                'code' => '0',
                'msg' => '管理员分配角色成功',
            ];
            return $data;

        } catch (\Exception $e) {
            DB::rollback();
            $data = [
                'code' => '1',
                'msg' => $e->getMessage(),
            ];
            return $data;
        }

    }
    

    // 系统设置
    public function setupindex()
    {
        // 渲染
        $page_title = '系统设置';
        return view('admin.system.setup.index', compact('page_title'));
    }


    /**
     * 公告列表
     *
     * @return \Illuminate\Http\Response
     */
    public function noticeindex()
    {
        // 列表
        $page_title = '公告列表';
        return view('admin.system.notice.index', compact('page_title'));
    }

    /**
     * 公告创建
     *
     * @return \Illuminate\Http\Response
     */
    public function noticecreate()
    {
        //
    }

    /**
     * 公告创建逻辑
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function noticestore(Request $request)
    {
        //
    }

    /**
     * 公告查看
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function noticeshow($id)
    {
        //
    }

    /**
     * 公告编辑
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function noticeedit($id)
    {
        //
    }

    /**
     * 公告更新逻辑
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function noticeupdate(Request $request, $id)
    {
        //
    }

    /**
     * 公告删除
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function noticedestroy($id)
    {

    }


    // 本人信息维护
    public function personalindex()
    {
        // 渲染
        $page_title = '本人信息维护';
        $id = Session::get('admin')['admin_id'];
        $personal = Manager::select(['id', 'name', 'mobile', 'password', 'email'])->find($id);
        return view('admin.system.personal.index', compact('page_title', 'personal'));
    }

    // 本人信息维护修改逻辑
    public function personalupdate(Request $request, $id)
    {
        // 验证
        $this->validate(request(), [
            'name' => 'required|min:2',
            // 'mobile' => 'required|unique:managers,mobile,'.$id.'|regex:/^1[345678][0-9]{9}$/',
            'email' => 'required|email|unique:managers,email,' . $id,
        ]);

        // 逻辑
        $manager = Manager::find($id);
        $name = request('name');
        // $mobile = request('mobile');
        $password = empty(request('password')) ? $manager->password : bcrypt(request('password'));
        $email = request('email');
        // if ($manager->update(compact('name', 'mobile', 'password', 'email'))) {
        if ($manager->update(compact('name', 'password', 'email'))) {
            $data = [
                'code' => '0',
                'msg' => '恭喜您，个人信息修改成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '很遗憾，个人信息修改失败',
            ];
        }
        // 返回
        return $data;
    }


    // 后台首页
    public function index(Request $request)
    {
        // 渲染
        $page_title = '后台默认首页';
        return view('admin.system.index', compact('page_title'));
    }



    // 账户类型列表
    public function accountindex(Request $request)
    {
        // 逻辑，判断是否有搜索关键词
        $name = request('name');

        // 渲染
        $page_title = '账户类型列表';
        $accounts = Account::select(['id', 'name', 'created_at', 'updated_at'])->orderBy('created_at', 'asc')->where('name', 'like', '%' . $name . '%')->paginate(10);
        return view('admin.system.account.index', compact('page_title', 'accounts', 'name'));
    }

    // 账户类型查看
    public function accountshow(Request $request, $id)
    {
        // 渲染
        $page_title = '账户类型查看';
        $account = Account::find($id);
        return view('admin.system.account.show', compact('page_title', 'account'));
    }

    // 账户类型增加
    public function accountcreate()
    {
        $page_title = '账户类型增加';
        return view('admin.system.account.create', compact('page_title'));
    }

    // 账户类型增加逻辑
    public function accountstore(Request $request)
    {
        // 验证
        $this->validate($request, [
            'name' => 'required|string|unique:accounts,name',
        ]);

        // 逻辑
        $name = request('name');
        $newid = Account::create(compact('name'));

        // 返回
        if ($newid) {
            $data = [
                'code' => '0',
                'msg' => '账户类型添加成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '账户类型添加失败',
            ];
        }
        return $data;

    }

    // 账户类型编辑
    public function accountedit($id)
    {
        // 渲染
        $page_title = '账户类型编辑';
        $model = Account::select(['id', 'name', 'created_at', 'updated_at'])->find($id);
        if ($model) {
            return view('admin.system.account.edit', compact('page_title', 'model'));
        } else {
            return view('errors.404');
        }
    }

    // 账户类型编辑逻辑
    public function accountupdate(Request $request, $id)
    {
        // 验证
        $this->validate($request, [
            'name' => 'required|string|unique:accounts,name,' . $id,
        ]);

        // 逻辑
        $name = request('name');
        $result = Account::find($id)->update(compact('name'));

        // 返回
        if ($result) {
            $data = [
                'code' => '0',
                'msg' => '账户类型修改成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '账户类型修改失败',
            ];
        }
        return $data;

    }

    // 账户类型删除
    public function accountdestroy($id)
    {
        $result = Account::destroy($id);
        if ($result == '1') {
            $data = [
                'code' => '0',
                'msg' => '删除成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg' => '删除失败',
            ];
        }
        // 返回
        return $data;
    }



}
