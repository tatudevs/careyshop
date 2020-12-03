{if is_array($data) OR is_object($data)}{foreach name="data" item="vo"}{$vo|raw}{/foreach}{else /}{$data|raw}{/if}
