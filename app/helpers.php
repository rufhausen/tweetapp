<?php

function removeAt($str)
{
    return preg_replace('/@/', '', $str);
}