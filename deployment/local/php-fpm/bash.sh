#!/bin/bash

echo -e "\e[32mConnecting to php-fpm container!"
echo -e "\e[97m"

docker compose exec cat_tree_php-fpm bash
