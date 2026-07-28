<?php

namespace RY\Tutor\Tutor;

defined('ABSPATH') or exit;

abstract class AbstractsApi
{
    public function set_do_die()
    {
        add_action('tutor_order_payment_updated', [$this, 'die_success'], 9999);
    }

    public function die_success($res)
    {
        exit('1|OK');
    }

    protected function order_no_to_trade_no($order_no, string $prefix = ''): string
    {
        return $prefix . $order_no . 'TS' . random_int(0, 9) . strrev((string) time());
    }

    protected function trade_no_to_order_no(string $trade_no, string $order_prefix = ''): int
    {
        return (int) substr($trade_no, strlen($order_prefix), strrpos($trade_no, 'TS'));
    }

    protected function get_item_name($item_name, $items)
    {
        if (empty($item_name)) {
            if (count($items)) {
                $item = reset($items);
                $item_name = $item['item_name'];
            }
        }
        $item_name = trim(wp_strip_all_tags($item_name));
        return str_replace(['^', '\'', '`', '!', '@', '＠', '#', '%', '&', '*', '+', '\\', '"', '<', '>', '|', '_', '[', ']'], '', $item_name);
    }
}
