<?php

namespace RY\Tutor\Tutor;

defined('ABSPATH') or exit;

use Tutor\Models\OrderMetaModel;

abstract class AbstractsApi
{
    protected const TRADENO_META_KEY = '';

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

    /**
     * @param int $order_ID
     * @param string $trade_no
     */
    protected function save_trade_no($order_ID, $trade_no): void
    {
        $list = $this->get_trade_no_list($order_ID);
        $list[$trade_no] = '';
        OrderMetaModel::update_meta($order_ID, static::TRADENO_META_KEY, $list);
    }

    /**
     * @param int $order_ID
     * @param string $trade_no
     * @param string $transaction_ID
     * @return void
     */
    protected function save_trade_transaction_id($order_ID, $trade_no, $transaction_ID): void
    {
        $list = $this->get_trade_no_list($order_ID);
        $list[$trade_no] = $transaction_ID;
        OrderMetaModel::update_meta($order_ID, static::TRADENO_META_KEY, $list);
    }

    /**
     * @param int $order_ID
     * @param string $trade_no
     */
    protected function is_used_trade_no($order_ID, $trade_no): bool
    {
        $list = $this->get_trade_no_list($order_ID);
        return isset($list[$trade_no]);
    }

    /**
     * @param int $order_ID
     */
    private function get_trade_no_list($order_ID): array
    {
        $list = OrderMetaModel::get_meta_value($order_ID, static::TRADENO_META_KEY, true);
        if (!is_array($list)) {
            $list = [];
        }
        return $list;
    }
}
