<?php
require_once getenv('RELOCATE_LIB') ?: getcwd().'/library.php';
$name='local_relocation_fixture_'.wp_generate_uuid4();
$old='https://old.example.test'; $new='https://a-longer.example.test';
$value=['links'=>[$old.'/a',$old.'.evil/b'],'count'=>2];
add_option($name,$value);
try {
    $plan=wor_plan($name,$old,$new);
    wor_apply($plan,false);
    global $wpdb;
    $raw=$wpdb->get_var($wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name=%s",$name));
    $stored=unserialize($raw,['allowed_classes'=>false]);
    if ($stored['links']!==[$new.'/a',$old.'.evil/b']) throw new RuntimeException('Incorrect serialized values');
    wor_apply($plan,true);
    if (get_option($name)!==$value) throw new RuntimeException('Undo lost fields');
    update_option($name,['edited'=>true]);
    try { wor_apply($plan,false); throw new LogicException('Stale plan accepted'); }
    catch (RuntimeException $expected) {}
    WP_CLI::success('Nested migration, rollback and intervening edit checks passed');
} finally { delete_option($name); }
