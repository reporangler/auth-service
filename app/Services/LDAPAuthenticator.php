<?php
namespace App\Services;

class LDAPAuthenticator
{
    public function auth($username, $password)
    {
        switch($username){
            case 'steve':
                return [
                    'id' => 3,
                    'username' => $username,
                    'groups' => [
                        $username,
                        'companyB',
                        'companyC',
                    ],
                ];
                break;

            case 'paul':
                return [
                    'id' => 4,
                    'username' => $username,
                    'groups' => [
                        $username,
                        'companyC',
                        'companyD',
                    ]
                ];
                break;
        }

        throw new NotFoundHttpException("This user does not exist");
    }
}
