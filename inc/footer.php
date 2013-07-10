<?php $path=is_numeric($GLOBALS['PAGE'])?'/':''; ?>
<div id="footer">
<p><small><a class="tool" target="_blank" href="https://github.com/GM-Script-Writer-62850/PHP-Scanner-Server/issues"><?php echo html($GLOBALS['NAME']); ?><span class="tip">Help and Support</span></a> version <a class="tool" href="<?php echo $path; ?>download.php?ver=<?php echo url($GLOBALS['VER']); ?>&amp;downloadServer"><?php echo html($GLOBALS['VER']); ?><span class="tip">Download</span></a> is
running on <a href="/"><?php echo html($_SERVER['SERVER_NAME']); ?></a> and there are <a href="<?php echo $path; ?>index.php?page=About">release notes</a> advailable.</small></p>
</div>
</div>
