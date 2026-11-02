<?php
require_once getenv('RELOCATE_LIB') ?: __DIR__.'/library.php';
try {
    $mode=array_shift($args);
    if ($mode==='plan' && count($args)===4) {
        [$name,$from,$to,$file]=$args;
        $plan=wor_plan($name,$from,$to);
        $encoded=json_encode($plan, JSON_THROW_ON_ERROR|JSON_PRETTY_PRINT|JSON_PRESERVE_ZERO_FRACTION);
        $handle=fopen($file,'xb');
        if (!$handle) throw new RuntimeException('Plan path exists or is unavailable');
        try { if (fwrite($handle,$encoded)!==strlen($encoded)) throw new RuntimeException('Incomplete plan write'); }
        finally { fclose($handle); }
    } elseif (in_array($mode,['apply','undo'],true) && count($args)===1) {
        wor_apply(json_decode(file_get_contents($args[0]),true,512,JSON_THROW_ON_ERROR),$mode==='undo');
    } else { throw new InvalidArgumentException('Expected plan NAME FROM TO FILE or apply/undo FILE'); }
    WP_CLI::success($mode.' complete');
} catch (Throwable $error) { WP_CLI::error($error->getMessage()); }
