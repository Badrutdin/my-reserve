<?php

namespace App\Security;

enum Permission: string
{
    case VIEW_CLIENTS = 'view_clients';
    case EDIT_CLIENTS = 'edit_clients';
    case DELETE_CLIENTS = 'delete_clients';
    case VIEW_ORDERS = 'view_orders';
    case EDIT_ORDERS = "edit_orders";
    case DELETE_ORDERS = 'delete_orders';
}
