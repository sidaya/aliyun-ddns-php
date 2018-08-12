# 阿里云DDNS PHP版

<h3>关于作者</h3>
<p>原作者地址https://github.com/kasuganosoras/SakuraDDNS</p>
<p>本人仅修复了获取ip的api失效问题和修改了api调用逻辑，兼容本网页在内网和公网环境</p>
<h3>这是什么？</h3>
<p>这是一个 PHP 编写的万网 DDNS 软件，让某些家中有公网 IP 的 dalao 们可以充分利用宽带资源做一切可以实现的事情。</p>

<h3>有什么用？</h3>
<p>众所周知，家用宽带的 IP 地址一般都是动态的，重启路由器后就会发生变化，所以必须要想办法让访问者获得最新的 IP 地址，那么 DDNS 就是一个很好的东西。</p>

<h3>如何使用？</h3>
<p>可以选择作为 Web 页面访问，也可以在命令行下运行，取决于个人需要。</p>
<p>无论是哪种方式，只要运行一次 sakuraddns.php，就会将指定域名的指定 A 记录对应记录值更新为当前设备的最新外网 IP 地址。</p>
<p>Access Key Id 和 Access Key Secret 可以在阿里云官网申请得到。</p>
<p>将对应配置修改好后，开始体验动态完成 IP 地址解析吧~</p>
