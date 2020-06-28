<?php 

namespace Yoop;

class RightsManager
{
    public function isGranted(...$roles)
    {
        $user = (new Session)->get('user');
    
        if(isset($user['roles'])) {
            foreach($roles as $role)
            {
                if(in_array($role,$user['roles'])) return true;
            }
        }
    
        return false;
    }

    public function isConnected()
    {
        $user = (new Session)->get('user');
        return (is_array($user) && isset($user['id']) && $user['id'] > 0);
    }
}
