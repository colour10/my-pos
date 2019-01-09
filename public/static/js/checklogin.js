// 执行跳转
if (window.localStorage.getItem('authorization') == null || window.localStorage.getItem('authorization') == 'null' || window.localStorage.getItem('authorization') == '') {
    window.location.href = '/agent/wx/login';
}