<?php

namespace App;

trait Functions
{
    /**
     * 任意値をハッシュ化した値を取得
     *
     * @param array $value
     * @return string
     */
    public function getHash(array $value)
    {
        // phpのhash関数は文字列にかけるものなので、一旦serializeで配列を文字列に変換してからhash関数をかける
        return hash('sha256', serialize($value));
    }
}
