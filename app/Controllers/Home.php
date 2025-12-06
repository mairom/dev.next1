<?php

namespace App\Controllers;


class Home extends BaseController
{
    
    public function index()
    {
		if (session()->has('usr_id')) {
            return redirect()->to(base_url('panel'));
        }
		
        $this->model('Settings_Model');
        $languages = $this->Settings_Model->get_languages();
		$lang = [];
		foreach ($languages as $language) {
			$lang[] = array(
				'name' => lang($language['name']),
				'foldername' => $language['foldername'],
				'id' => $language['id'],
				'langcode' => $language['langcode']
			);
		}

		$data['languages'] = $lang;

        return view('login/login', $data);
    }
}
