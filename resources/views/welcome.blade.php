<!DOCTYPE html>
<html>
<head>
    <title>{{ config('app.name') }}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="icon" href="https://www.qiyichao.cn/image/fire-orange.png">
    <link rel="apple-touch-icon" href="https://www.qiyichao.cn/image/fire-orange.png">
    <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
    <!--
        Light - Shadowsocks 多区域多节点控制面板
        https://www.qiyichao.cn/
        作者 cenegd <cenegd@live.com>
    -->
</head>
<body>
    <div id="app">
    </div>
    <script type="text/javascript">
        window.Laravel = {
            csrfToken: "{{ csrf_token() }}"
        }
    </script>
    <script type="text/javascript" src="{{ mix('js/app.js') }}"></script>
</body>
</html>