<?php
function wor_origin(string $origin): void {
    if (!preg_match('~^https://[a-z0-9.-]+(?::[0-9]+)?$~D',$origin)) throw new InvalidArgumentException('Expected HTTPS origin without trailing slash');
}
function wor_map(mixed $value,string $from,string $to): mixed {
    if (is_array($value)) {
        foreach ($value as $key=>$entry) $value[$key]=wor_map($entry,$from,$to);
        return $value;
    }
    if (is_object($value) || is_resource($value)) throw new RuntimeException('Unsupported option type');
    if (!is_string($value) || !str_starts_with($value,$from)) return $value;
    $suffix=substr($value,strlen($from));
    return ($suffix==='' || str_contains('/?#',$suffix[0])) ? $to.$suffix : $value;
}
function wor_plan(string $name,string $from,string $to): array {
    wor_origin($from); wor_origin($to);
    if ($from===$to) throw new InvalidArgumentException('Origins must differ');
    $missing=new stdClass();
    $before=get_option($name,$missing);
    if ($before===$missing) throw new RuntimeException('Missing option');
    return ['name'=>$name,'before'=>$before,'after'=>wor_map($before,$from,$to)];
}
function wor_apply(array $plan,bool $undo): void {
    $name=$plan['name'];
    [$expected,$target]=$undo ? [$plan['after'],$plan['before']] : [$plan['before'],$plan['after']];
    if (get_option($name,new stdClass()) !== $expected) throw new RuntimeException('Option changed since plan');
    if ($target !== $expected && !update_option($name,$target)) throw new RuntimeException('Update failed');
}
