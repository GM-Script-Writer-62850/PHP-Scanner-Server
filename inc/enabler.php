<div class="box box-full"><h2>Scanner Access Enabler (Quick Start)</h2>
<p>
If a scanner shows with <code>scanimage -L</code> and is not detected by the server scanner the problem is permission.<br/>
To enable access <a href="http://ubuntuforums.org/member.php?u=162029">jhansonxi</a> has developed a application that will enable access a copy is included with the PHP Server Scanner.<br/>
To install it <a href="scanner-access-enabler-<?php echo $SAE_VER; ?>.tar.bz2">download the archive</a> and extract it. Then move the script to <code>/usr/local/bin/scanner-access-enabler</code> and set it for root:root ownership with rwxr-xr-x (0755) permissions.
Then move the destop menu entry to the <code>/usr/local/share/applications</code> directory with root:root ownership and rw-r--r-- (0644) permissions. The applicaion will now be under System -> Administration in Ubuntu.
Some scanners will need to have this done every time you boot.<br/>
If you have to run it every boot add <code>/usr/local/bin/scanner-access-enabler -s</code> before <code>exit 0</code> in <code>/etc/rc.local</code> on its own line and you are good to go.<br/><br/>
So you just want the terminal commands, I will assume you just opened a terminal and extracted the archive to your desktop
<pre># installs application
sudo mv Desktop/scanner-access-enabler /usr/local/bin
# makes next command work
sudo mkdir /usr/local/share/applications
# add menu entry under the system menu
sudo mv Desktop/scanner-access-enabler.desktop /usr/local/share/applications
# enable scanner(s)
sudo /usr/local/bin/scanner-access-enabler
# re-enable scanners every boot
sudo nano /etc/rc.local
# Add "/usr/local/bin/scanner-access-enabler -s" before "exit 0" on its own line without quotes
# press [ctrl]+[O] then [enter] to save
# press [ctrl]+[X] to exit nano</pre>
</p>
</div>

<div class="box box-full"><h2>Scanner Access Enabler (Full Details)</h2>
<p>There is a problem with scanner device permissions on Ubuntu. Regular users (<code>UID&gt;999</code>) can access libsane applications like Xsane and <a href="https://launchpad.net/simple-scan">Simple Scan</a> without problems.  PHP Scanner Server, which is running in Apache as www-data, can't access them without a <code>chmod o+rw</code> on each scanner device.  Nobody seems to know <a href="https://answers.launchpad.net/ubuntu/+question/127223">how the permissions work</a> so this has to be fixed manually in a terminal.  This is not n00b friendly so I created a GUI application that automatically changes the permissions of every scanner device.</p>
<p>The application relies on <a href="http://www.sane-project.org/man/scanimage.1.html"><code>scanimage</code></a> and <a href="http://www.sane-project.org/man/sane-find-scanner.1.html">sane-find-scanner</a> utilities to identify scanner device ports then simply does a <code>chmod</code> against all of them.  It supports USB, SCSI, and optionally parallel port (-p parameter) scanners and has been tested against the same ones I used for my <a href="http://jhansonxi.blogspot.com/2010/10/patch-for-linux-scanner-server-v12.html">LSS patch</a>.  It uses the same universal dialog code as <a href="http://jhansonxi.blogspot.com/2010/09/webcam-server-dialog-basic-front-end-to.html">webcam-server-dialog</a> so it should work with almost any desktop environment.</p><p>To install first <a href="scanner-access-enabler-1.2.tar.bz2">download the archive</a> and extract the contents.  Move the script to "<code>/usr/local/bin/scanner-access-enabler</code>" and set it for root:root ownership with <code>rwxr-xr-x</code> (0755) permissions.  Copy the <a href="http://standards.freedesktop.org/desktop-entry-spec/latest/">destop menu entry</a> to the <code>/usr/local/share/applications</code> directory with <code>root:root</code> ownership and <code>rw-r--r--</code> (0644) permissions.  You may have to edit the desktop file as it uses gksudo by default.  On KDE you may want to change the Exec entry to use <code>kdesudo</code> instead.  If you specify the -p option on the Exec line you may have to quote everything after gk/kdesudo.  If you don't have one of the GUI dialoger utilities installed and plan on using dialog or whiptail then you need to set "Terminal=true" else you won't see anything.</p><p>On Ubuntu the menu item will be found under System &gt; Administration.  If you want users to be able to activate scanners without a password and admin group membership, you can add an exception to the end of "<code>/etc/sudoers</code>" file.  Simply run "sudo visudo" and enter the following:</p>
<p><code># Allow any user to fix SCSI scanner port device permissions</code><br>
<code>ALL ALL=NOPASSWD: /usr/local/bin/scanner-access-enabler *</code></p>
<p>While you can use any editor as root to change the file, visudo checks for syntax errors before saving as a mistake can disable sudo and prevent you from fixing it easily.  If you mess it up, you can reboot and use Ubuntu recovery mode or a LiveCD to fix it.</p><p>Update:  I released v1.1 which adds filtering for "net:" devices from saned connections.  This didn't affect the permission changes but made for a crowded dialog with both the raw and net devices shown.</p>
</div>
