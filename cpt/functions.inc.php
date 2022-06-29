<?php
namespace workout_manager;

function clean_pagination($pagination){
    return preg_replace('~(<h2\\s(class="screen-reader-text")(.*)[$>])(.*)(</h2>)~ui', '', $pagination);
}