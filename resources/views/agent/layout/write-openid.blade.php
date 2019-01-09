<!-- 记录openid Start -->
@if ($user)
<script type="text/javascript">
    window.localStorage.setItem('openid', "{{ $user['id'] }}");
</script>
@endif
<!-- 记录openid End -->
