<?php

foreach(glob("./server/*.php") as $file)
    require_once $file;

foreach(glob("./server/controllers/*.php") as $file)
    require_once $file;

foreach(glob("./server/models/*.php") as $file)
    require_once $file;

foreach(glob("./server/persistence/*.php") as $file)
    require_once $file;