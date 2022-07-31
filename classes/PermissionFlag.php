<?php

final class PermissionFlag extends Enum
{
    public const VIEW_METH_TRANSACTION = 1;
    public const CREATE_METH_TRANSACTION = 2;
    public const CREATE_SLAVE_METH_TRANSACTION = 4;
    public const DELETE_METH_TRANSACTION = 8;
    public const VIEW_PLAYER = 16;
    public const VIEW_ALL_PLAYERS = 32;
    public const DELETE_PLAYER = 64;
    public const DELETE_ALL_PLAYERS = 128;
    public const VIEW_ACCOUNTS = 256;
    public const CREATE_ACCOUNT = 512;
    public const DELETE_ACCOUNT = 1024;
}