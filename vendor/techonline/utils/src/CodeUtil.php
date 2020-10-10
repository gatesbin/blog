<?php

namespace TechOnline\Utils;


class CodeUtil
{
    public static function removePHPComments($code)
    {
        $commentTokens = array(T_COMMENT);
        if (defined('T_DOC_COMMENT')) {
            $commentTokens[] = T_DOC_COMMENT;         }
        if (defined('T_ML_COMMENT')) {
            $commentTokens[] = T_ML_COMMENT;          }
        $codeNew = [];
        $tokens = token_get_all($code);
        $prevEmpty = false;
        foreach ($tokens as $token) {
            if (is_array($token)) {
                if (in_array($token[0], $commentTokens)) {
                    continue;
                }
                $token = $token[1];
            }
            $codeNew[] = $token;
        }
        return join('', $codeNew);
    }
}
