<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace support;

/**
 * Class Request
 * @package support
 * @property int $user_id 用户ID
 */
class Request extends \Webman\Http\Request
{
    /**
     * 设置$request数据，自动覆盖更新
     * @param string $method
     * @param array $data
     */
    function setParams(string $method, array $data)
    {
        $method = strtolower($method);
        if (!isset($this->_data[$method])) {
            if ($method == 'post'){
                $this->parsePost();
            }
            if ($method == 'get'){
                $this->parseGet();
            }

        }
        $rawData = $this->_data ?: [];// 获取原数据
        $newData = $rawData; // 复制原始数据
        $newData[$method] = array_merge($newData[$method] ?? [], $data); // 合并特定方法的数据
        $this->_data = $newData; // 更新对象数据
    }

    /**
     * 当前访问端
     *
     * @param string $terminal
     *
     * @return bool
     */
    public function isTerminal(string $terminal): bool
    {
        return strtolower($this->getFromType()) === $terminal;
    }

    /**
     * 获取用户访问端
     *
     * @return array|string|null
     */
    public function getFromType(): array|string|null
    {
        return $this->header('Form-type', '');
    }

}