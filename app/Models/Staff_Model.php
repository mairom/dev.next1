<?php

namespace App\Models;

use CodeIgniter\Model;

class Staff_Model extends Model
{

    function get_staff($id)
    {
        $builder = \Config\Database::connect()->table('staff');
        $builder->select('*, languages.name as stafflanguage, departments.name as department, staff.id as id, staff.role_id as role_id');
        $builder->join('departments', 'staff.department_id = departments.id', 'left');
        $builder->join('languages', 'staff.language = languages.foldername', 'left');
        $builder->join('roles', 'staff.role_id = roles.role_id', 'left');
        $builder->where('staff.id_company', session()->get('id_company'));

        return $builder->where('staff.id', $id)->get()->getRowArray();
    }

    function get_name_staff($id)
    {
        $builder = \Config\Database::connect()->table('staff');
        $builder->where('staff.id_company', session()->get('id_company'));

        return $builder->where('staff.id', $id)->get()->getRowArray();
    }

    function get_staffSearch($q)
    {
        $builder = \Config\Database::connect()->table('staff');
        $builder->select('staffname as text, id');
        $builder->where('staff.id_company', session()->get('id_company'));
        $builder->like('staff.staffname', $q);

        return $builder->get()->getResult();
    }

    function validate_user_password($id, $password)
    {
        $builder = \Config\Database::connect()->table('staff');
        $builder->where('id', $id);
        $builder->where('password', md5($password));

        $login = $builder->get()->getResult();

        return count($login) === 1;
    }

    function get_all_staff($global = false, $inactives = false)
    {
        $builder = \Config\Database::connect()->table('staff');
        $builder->select('*, departments.name as department, staff.id as id, staff.id_company as id_company_staff');
        $builder->join('departments', 'staff.department_id = departments.id', 'left');

        if (!$global) {
            $builder->where('staff.id_company', session()->get('id_company'));
        } elseif (session()->get('super_admin') !== "1") {
            $builder->where('staff.id_company', session()->get('id_company'));
        }
        if (!$inactives) {
            $builder->where('staff.inactive IS NULL');
        }

        return $builder->get()->getResultArray();
    }

    function get_all_admins()
    {
        $builder = \Config\Database::connect()->table('staff');
        $builder->select('email');
        $builder->where('role_id', 1);
        $builder->orderBy('id', 'asc');
        $builder->limit(1);

        return $builder->get()->getRowArray();
    }



    function add_staff($params)
    {

        $builder = $this->db->table('staff');
        $builder->insert($params);
        $staffmember = $this->db->insertID();

        $appconfig = get_appconfig();
        $number = $appconfig['staff_series'] ?? $staffmember;
        $staff_number = $appconfig['staff_prefix'] . $number;
        $builder->where('id', $staffmember)->update(['staff_number' => $staff_number]);

        $workplan = [
            ['day' => lang2('monday'), 'status' => true, 'start' => '09:00', 'end' => '18:00', 'breaks' => ['start' => '14:30', 'end' => '15:00']],
            ['day' => lang2('tuesday'), 'status' => true, 'start' => '09:00', 'end' => '18:00', 'breaks' => ['start' => '14:30', 'end' => '15:00']],
            ['day' => lang2('wednesday'), 'status' => true, 'start' => '09:00', 'end' => '18:00', 'breaks' => ['start' => '14:30', 'end' => '15:00']],
            ['day' => lang2('thursday'), 'status' => true, 'start' => '09:00', 'end' => '18:00', 'breaks' => ['start' => '14:30', 'end' => '15:00']],
            ['day' => lang2('friday'), 'status' => true, 'start' => '09:00', 'end' => '18:00', 'breaks' => ['start' => '14:30', 'end' => '15:00']],
            ['day' => lang2('saturday'), 'status' => false, 'start' => '', 'end' => '', 'breaks' => ['start' => '', 'end' => '']],
            ['day' => lang2('sunday'), 'status' => false, 'start' => '', 'end' => '', 'breaks' => ['start' => '', 'end' => '']],
        ];

        $this->db->table('staff_work_plan')->insert([
            'staff_id' => $staffmember,
            'work_plan' => json_encode($workplan),
        ]);

        $loggedinuserid = session()->get('usr_id');
        $this->db->table('logs')->insert([
            'date' => date('Y-m-d H:i:s'),
            'detail' => sprintf('<a href="staff/staffmember/%d"> %s</a> %s <a href="staff/staffmember/%d">%s</a>.', $loggedinuserid, session()->get('staffname'), lang2('added'), $staffmember, get_number('staff', $staffmember, 'staff', 'staff')),
            'staff_id' => $loggedinuserid,
        ]);

        return $staffmember;
    }


    function update_language($id, $language)
    {
        $builder = \Config\Database::connect()->table('staff');
        $builder->where('id', $id)->update(['language' => $language]);
    }

    function update_staff($id, $params)
    {

        $builder = $this->db->table('staff');
        $appconfig = get_appconfig();

        $staff_data = $this->get_staff($id);

        if (empty($staff_data['staff_number'])) {
            $number = $appconfig['staff_series'] ?? $id;
            $staff_number = $appconfig['staff_prefix'] . $number;
            $builder->where('id', $id)->update(['staff_number' => $staff_number]);

            if (!empty($appconfig['staff_series'])) {
                $staff_number = $appconfig['staff_series'] + 1;
                $this->Settings_Model->increment_series('staff_series', $staff_number);
            }
        }

        $builder->where('id', $id);
        $builder->update($params);

        $this->db->table('logs')->insert([
            'date' => date('Y-m-d H:i:s'),
            'detail' => sprintf('<a href="staff/staffmember/%d"> %s</a> %s <a href="staff/staffmember/%d">%s</a>.', session()->get('usr_id'), session()->get('staffname'), lang2('updated'), $id, get_number('staff', $id, 'staff', 'staff')),
            'staff_id' => session()->get('usr_id'),
        ]);
    }

    function delete_staff($id, $number)
    {
        $tables = [
            'appointments',
            'comments',
            'db_backup',
            'deposits',
            'discussions',
            'discussion_comments',
            'events',
            'expenses',
            'invoices',
            'leads',
            'orders',
            'payments',
            'projectmembers',
            'projects',
            'proposals',
            'purchases',
            'subtasks',
            'tasks',
            'tasktimer',
            'ticketreplies',
            'tickets',
            'vendors',
            'vendor_sales',
            'webleads'
        ];



        foreach ($tables as $table) {
            $count = $this->db->table($table)->where('staff_id', $id)->countAllResults();
            if ($count > 0) {
                return false;
            }
        }

        $builder = $this->db->table('staff');
        $builder->delete(['id' => $id]);

        $this->db->table('privileges')->delete(['relation' => $id, 'relation_type' => 'staff']);

        $staffname = session()->get('staffname');
        $loggedinuserid = session()->get('usr_id');

        $this->db->table('logs')->insert([
            'date' => date('Y-m-d H:i:s'),
            'detail' => sprintf('<a href="staff/staffmember/%d"> %s</a> %s %s', $loggedinuserid, $staffname, lang2('deleted'), $number),
            'staff_id' => $loggedinuserid,
        ]);

        return true;
    }



    function delete_avatar($id)
    {

        $builder = $this->db->table('staff');
        $builder->where('id', $id)->update(['staffavatar' => 'n-img.jpg']);
    }

    function total_sales_by_staff($id)
    {

        $builder = $this->db->table('sales');
        $builder->selectSum('total');
        $builder->where('staff_id', $id);
        $result = $builder->get()->getRow();
        return $result ? $result->total : 0;
    }

    function total_customer_by_staff($id)
    {

        $builder = $this->db->table('customers');
        $builder->where('staff_id', $id);
        return $builder->countAllResults();
    }

    function total_ticket_by_staff($id)
    {

        $builder = $this->db->table('tickets');
        $builder->where('staff_id', $id);
        return $builder->countAllResults();
    }

    function isDuplicate($email)
    {

        $builder = $this->db->table('staff');
        $builder->where('email', $email);
        return $builder->countAllResults() > 0;
    }

    function get_staff_email($id)
    {

        $builder = $this->db->table('staff');
        $builder->where('id', $id);
        return $builder->get()->getRowArray();
    }

    function insertToken($user_id)
    {

        $token = substr(sha1(rand()), 0, 30);
        $date = date('Y-m-d');

        $data = [
            'token' => $token,
            'user_id' => $user_id,
            'created' => $date,
        ];

        $res = $this->db->table('tokens')->insert($data);

      
        return $token . $user_id;
    }

    function isTokenValid($token)
    {

        $tkn = substr($token, 0, 30);
        $uid = substr($token, 30);

        $builder = $this->db->table('tokens');
        $builder->where('token', $tkn);
        $builder->where('user_id', $uid);
        $result = $builder->get()->getRow();

        if ($result) {
            $createdTS = strtotime($result->created);
            $todayTS = strtotime(date('Y-m-d'));

            if ($createdTS == $todayTS) {
                return $this->getUserInfo($result->user_id);
            }
        }
        return false;
    }

    function getUserInfo($id)
    {

        $builder = $this->db->table('staff');
        $builder->where('id', $id);
        $result = $builder->get()->getRow();

        if ($result) {
            return $result;
        } else {
            error_log('no user found getUserInfo(' . $id . ')');
            return false;
        }
    }

    function updateUserInfo($post)
    {

        $data = [
            'password' => $post['password'],
            'last_login' => date('Y-m-d h:i:s A'),
            'inactive' => $this->inactive[1]
        ];

        $builder = $this->db->table('staff');
        $builder->where('id', $post['user_id']);
        $builder->update($data);

        if ($this->db->affectedRows() === 0) {
            error_log('Unable to updateUserInfo(' . $post['user_id'] . ')');
            return false;
        }

        return $this->getUserInfo($post['user_id']);
    }



    function getUserInfoByEmail($email)
    {

        $builder = $this->db->table('staff');
        $builder->where('email', $email);
        $query = $builder->get()->getRow();

        if ($query) {
            return $query;
        } else {
            error_log('no user found getUserInfoByEmail(' . $email . ')');
            return false;
        }
    }

    function updatePassword($post)
    {

       

        $builder = $this->db->table('staff');
        $builder->where('id', $post['user_id']);
        $builder->update(['password' => $post['password']]);

        if ($this->db->affectedRows() === 0) {
            error_log('Unable to updatePassword(' . $post['user_id'] . ')');
            return false;
        }

        return true;
    }

    function get_work_plan($id)
    {

        $builder = $this->db->table('staff_work_plan');
        $builder->where('staff_id', $id);
        return $builder->get()->getRowArray();
    }

    function get_horasTrabalho($id)
    {
        $hora_trabalho = [];
        $horas_trabalho = [];
        $horas = json_decode($this->get_work_plan($id)['work_plan'], true);

        $diasemana = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
        $diasemana_numero = date('w', strtotime(date('Y-m-d')));
        $dia = $diasemana[$diasemana_numero];

        if ($horas) {
            foreach ($horas as $index => $row) {
                if ($row['day'] == $dia) {
                    $hora_trabalho = $row;
                }
            }
            if (isset($hora_trabalho['breaks']) && !empty($hora_trabalho['breaks'])) {
                $horas_trabalho = [
                    'start01' => $hora_trabalho['start'],
                    'end01' => $hora_trabalho['breaks']['start'],
                    'start02' => $hora_trabalho['breaks']['end'],
                    'end02' => $hora_trabalho['end'],
                ];
            } else {
                $horas_trabalho = [
                    'start01' => $hora_trabalho['start'],
                    'end01' => $hora_trabalho['end'],
                    'start02' => '00:00:00',
                    'end02' => '00:00:00',
                ];
            }
        }

        return $horas_trabalho;
    }

    function get_role_type($role_id)
    {

        $builder = $this->db->table('roles');
        $builder->where('role_id', $role_id);
        return $builder->get()->getRow()->role_type;
    }

    function restore_workplan($staff_id)
    {

        $builder = $this->db->table('staff_work_plan');
        $builder->where('staff_id', $staff_id);
        $builder->delete();

        $workplan = [
            [
                'day' => lang2('monday'),
                'status' => true,
                'start' => '09:00',
                'end' => '18:00',
                'breaks' => [
                    'start' => '14:30',
                    'end' => '15:00',
                ],
            ],
            [
                'day' => lang2('tuesday'),
                'status' => true,
                'start' => '09:00',
                'end' => '18:00',
                'breaks' => [
                    'start' => '14:30',
                    'end' => '15:00',
                ],
            ],
            [
                'day' => lang2('wednesday'),
                'status' => true,
                'start' => '09:00',
                'end' => '18:00',
                'breaks' => [
                    'start' => '14:30',
                    'end' => '15:00',
                ],
            ],
            [
                'day' => lang2('thursday'),
                'status' => true,
                'start' => '09:00',
                'end' => '18:00',
                'breaks' => [
                    'start' => '14:30',
                    'end' => '15:00',
                ],
            ],
            [
                'day' => lang2('friday'),
                'status' => true,
                'start' => '09:00',
                'end' => '18:00',
                'breaks' => [
                    'start' => '14:30',
                    'end' => '15:00',
                ],
            ],
            [
                'day' => lang2('saturday'),
                'status' => false,
                'start' => '',
                'end' => '',
                'breaks' => [
                    'start' => '',
                    'end' => '',
                ],
            ],
            [
                'day' => lang2('sunday'),
                'status' => false,
                'start' => '',
                'end' => '',
                'breaks' => [
                    'start' => '',
                    'end' => '',
                ],
            ],
        ];

        $builder->insert([
            'staff_id' => $staff_id,
            'work_plan' => json_encode($workplan),
        ]);

        return true;
    }
}
