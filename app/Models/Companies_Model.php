<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\Privileges_Model;

class Companies_Model extends Model
{
    protected $table =  'companies';

    function get_companies()
    {
        $builder = $this->db->table('companies');
        $builder->orderBy('nm_company', 'asc');
        $result = [];

        foreach ($builder->get()->getResultArray() as $company) {

            $company['ativo'] = $company['status'] == '1' ? true : false;

            $company['usuarios'] = $this->get_usuarios($company['id_company']);
            $company['permissoes'] = $this->get_permissions($company['id_company']);
            $company['modalReport'] = $this->get_reports($company['id_company'], $company['usuarios']);

            $result[] = $company;
        }

        return $result;
    }

    function get_reports($id, $usuarios)
    {
        $builder = $this->db->table('companies_reports');
        $builder->where('id_company', $id);
        $data = $builder->get()->getRowArray();

        if ($data) {
            $emails = explode(',', $data['emails']);
            $data['emails'] = $emails;
            $data['TodosEmails'] = $emails;
            $permissoes = explode(',', $data['permissoes']);

            $permissoesR = [];
            foreach ($permissoes as $r) {
                $permissoesR[$r] = true;
            }
            $data['permission']['permission'] = $permissoesR;
        } else {
            $data['status'] = '1';
            $data['frequencia'] = '7';
            $data['data'] = date('Y-m-d H:i:s');
            $data['TodosEmails'] = [];
        }

        if ($usuarios) {
            foreach ($usuarios as $user) {
                if (!in_array($user['email'], $data['TodosEmails'])) {
                    $data['TodosEmails'][] = $user['email'];
                }
            }
        }

        return  $data;
    }

    function get_companiesReports()
    {
        $builder = $this->db->table('companies_reports');
        $builder->select('*, (SELECT data FROM companies_reports_envio WHERE companies_reports_envio.id_company = companies.id_company ORDER BY id_envio DESC LIMIT 1) as ultimoEnvio');
        $builder->join('companies', 'companies.id_company = companies_reports.id_company', 'left');
        $data = $builder->get()->getResultArray();

        return $data;
    }

    function get_permissions($id)
    {
        $Privileges_Model = new Privileges_Model();
        $permissoes = $Privileges_Model->get_all_permissionsByCompany($id);
        foreach ($permissoes as $index => $row) {
            $permissoes[$index]['permission_key'] = lang($row['permission']);
        }
        //  $nvPermissoes = [];
        //  $corte = count($permissoes) / 2;
        //  for ($i = 0; $i < count($permissoes); $i = $i + $corte) {
        //      $nvPermissoes[] =  array_slice($permissoes, $i, $i + $corte);
        //  }
        return $permissoes;
    }

    function get_company($id)
    {
        $builder = $this->db->table('companies');
        $builder->where('id_company', $id);
        return $builder->get()->getRowArray();
    }

    function get_usuarios($id)
    {
        $builder = $this->db->table('staff');
        $builder->where('id_company', $id);
        return $builder->get()->getResultArray();
    }

    function insertSettings($company)
    {

        $params = [
            'languageid' => "portuguese_br",
            'default_timezone' => "America/Sao_Paulo",
            'crm_name' => 'Next One',
            'accepted_files_formats' => 'jpg,jpeg,doc,png,txt,docx',
            'id_company' => $company
        ];
        $this->db->table('settings')->insert($params);


        $params2 = [
            'id_company' => $company,
            'role_name' => "ADMIN",
            'role_type' => "admin",
            'role_createdat' => date('Y-m-d H:i:s'),
            'role_updatedat' => date('Y-m-d H:i:s'),
            'created_by' => session()->usr_id != null ? session()->usr_id : '',
        ];
        $this->db->table('roles')->insert($params2);
        $role_id = $this->db->insertID();
        return  $role_id;
    }



    function insertLeadsAtvSelect($company)
    {

        $this->db->query("INSERT INTO `leads_atv_select` (`id_atv`, `id_company`, `nm_atividade_select`, `atv_ft`)
        VALUES
        (NULL, '$company', 'Apresentação por e-mail', 'assets/img/atividades/send.png'),
        (NULL, '$company', 'Reunião presencial', 'assets/img/atividades/people2.png'),
        (NULL, '$company', 'Reunião Remota (call)', 'assets/img/atividades/monitor.png'),
        (NULL, '$company', 'Follow up por e-mail', 'assets/img/atividades/mail.png'),
        (NULL, '$company', 'Follow up mensagem', 'assets/img/atividades/message.png'),
        (NULL, '$company', 'Follow up ligação', 'assets/img/atividades/phone.png'),
        (NULL, '$company', 'Café', 'assets/img/atividades/almoço.png'),
        (NULL, '$company', 'Minuta de Contrato', 'assets/img/atividades/document.png'),
        (NULL, '$company', 'Contrato pendente de assinatura', 'assets/img/atividades/pen.png'),
        (NULL, '$company', 'Whatsapp', 'assets/img/atividades/whatsapp_-_cópia.png'),
        (NULL, '$company', 'Skype', 'assets/img/atividades/skype.png'),
        (NULL, '$company', 'Linkedin', 'assets/img/atividades/linkedin.png'),
        (NULL, '$company', 'Instagram', 'assets/img/atividades/insta.png'),
        (NULL, '$company', 'Bate-papo', 'assets/img/atividades/edit.png'),
        (NULL, '$company', 'Retorno do Lead', 'assets/img/atividades/followup.png'),
        (NULL, '$company', 'Retorno da nossa empresa ', 'assets/img/atividades/repeat.png'),
        (NULL, '$company', 'Ligação', 'assets/img/atividades/phone1.png'),
        (NULL, '$company', 'Proposta Comercial', 'assets/img/atividades/dolar.png'),
        (NULL, '$company', 'SMS', 'assets/img/atividades/message1.png'),
        (NULL, '$company', 'Trial', 'assets/img/atividades/atividade1.png'),
        (NULL, '$company', 'Em análise', 'assets/img/atividades/watch.png'),
        (NULL, '$company', 'Solicitou prazo', 'assets/img/atividades/time.png'),
        (NULL, '$company', 'Atividade', 'assets/img/atividades/activity.png'),
        (NULL, '$company', 'Lead One', 'assets/img/atividades/leadone.png'),
        (NULL, '$company', 'Histórico Anterior', 'assets/img/atividades/open.png')");
    }


    function insertEmailTemplates($company)
    {
        $this->db->query("INSERT INTO `email_templates` (`id`, `id_company`, `relation`, `name`, `subject`, `message`, `from_name`, `status`, `display`, `attachment`, `anexo`) VALUES
        (NULL, '$company' 'invoice', 'invoice_message', 'Invoice with number {invoice_number} created', '<p><span><strong>INVOICE {invoice_number}</strong></span><br><br></p><div>Hello {customer},</div><div><br></div><div>We have prepared the following invoice for you: # <strong>{invoice_number}</strong></div><div><br></div><div>Invoice status: <strong>{invoice_status}</strong></div><div><br></div><div>You can view the invoice on the following link: <strong>{invoice_number}</strong></div><div><br></div><div>Please contact us for more information.</div><div><br></div><div>Kind Regards,</div><div><br></div><div><strong>{name},</strong></div><div>{email_signature}</div>', 'Ciuis CRM', 1, 1, 1, '97d46bec991bde2916ad841bda9dd6c0.csv'),
        (NULL, '$company' 'invoice', 'invoice_reminder', 'Send Invoice', '<h1><span xss=removed>DOCTYPE</span></h1>\n<p xss=removed>DOCTYPE</p>', 'Ciuis CRM', 1, 0, 0, NULL),
        (NULL, '$company' 'invoice', 'invoice_payment', 'Invoice Payment Recorded', '<p><span>Hello {customer}<br><br></span>Thank you for the payment. Find the payment details below:<br><br>-------------------------------------------------<br><br>Amount: <strong>{payment_total}<br></strong>Date: <strong>{payment_date}</strong><br>Invoice number: <span><strong># {invoice_number}<br><br></strong></span>-------------------------------------------------<br><br>You can always view the invoice for this payment at the following link: <a href=\"{invoice_link}\" data-mce-href=\"{invoice_link}\">{invoice_number}</a></p><p><a>View Invoice</a><br><br>We are looking forward working with you.<br><br><span>Kind Regards,</span></p><p><strong>{name}</strong><br><span>{email_signature}</span></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'invoice', 'invoice_overdue', 'Invoice Overdue Notice - {invoice_number}', '<p><span>Hi {customer},</span><br><br><span>This is an overdue notice for invoice <strong># {invoice_number}</strong></span><br><br><span>This invoice was due: {invoice_duedate}</span><br><br><span>You can view the invoice on the following link: <a>{invoice_number}</a></span><br><br><span>Kind Regards,</span></p><p><strong>{name}</strong><br><span>{email_signature}</span></p>', 'Ciuis CRM', 1, 0, 0, NULL),
        (NULL, '$company' 'customer', 'new_contact_added', 'Welcome aboard', '<p>Dear {customer},<br><br>Thank you for registering.<br><br>We just wanted to say welcome.<br><br>Please contact us if you need any help.<br><br>Click here to view your profile: <a href=\"{app_url}\" data-mce-href=\"{app_url}\" style=\"\">{app_url}</a></p><p>Your login details:</p><p>Email: <span><strong>{login_email}</strong></span></p><p>Password: <span><strong>{login_password}</strong></span><br><br>Kind Regards,</p><p><strong>{name}</strong>,<br>{email_signature}<br><br>(This is an automated email, so please don\'t reply to this email address)</p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'customer', 'new_customer', 'New Customer Registration', '<p>Hello Admin.<br><br>New customer registration on your customer portal:<br></p><p><strong>Type</strong>: {customer_type}<br><strong>Name: </strong>{name}<br><strong>Email:</strong> {customer_email}<br><br>Best Regards<br></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'staff', 'new_staff', 'New Staff Added (Welcome Email)', '<p>Hi {staff},<br><br>You are added as member on our CRM.<br><br>Please use the following logic credentials:<br><br><strong>Email:</strong> {staff_email}<br><strong>Password:</strong> {password}<br><br>Click <span><span><a href=\"{login_url}\" data-mce-href=\"{login_url}\">here </a> </span></span>to login in the dashboard.<br><br>Best Regards,<br><strong>{name}</strong>,<br>{email_signature}<br></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'staff', 'forgot_password', 'Reset password', '<h2><span>Reset password</span></h2><p>Hi {staffname},</p><p><br>Forgot your password?<br>To create a new password, just follow this link:<br><br><a href=\"{password_url}\" data-mce-href=\"{password_url}\">Reset Password</a><br><br>You received this email, because it was requested by a user. This is part of the procedure to create a new password on the system. If you DID NOT request a new password then please ignore this email and your password will remain the same. <br></p><p>Regards,</p><p>{email_signature}<br></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'staff', 'password_reset', 'Your password has been changed', '<p><span><strong>You have changed your password.<br></strong></span><br>Please, keep it in your records so you don\'t forget it.<br><br>Your email address for login is: <span style=\"color: rgb(0, 0, 255)\">{staff_email}</span><br><br>If this was not you, please contact us.<br></p><p>Regards,<br>{email_signature}<br></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'staff', 'reminder_email', 'Send Invoice', 'Im sending Invoice', 'Ciuis CRM', 1, 0, 0, NULL),
        (NULL, '$company' 'task', 'new_task_assigned', 'New Task Assigned to You - {task_name}', '<p><span>Dear {staffname},</span><br><br><span>You have been assigned to a new task:</span><br><br><span><strong>Name:</strong> {task_name}</span></p><p><strong>Start Date:</strong> {task_startdate}</p><p><span><strong>Due date:</strong> {task_duedate}</span></p><p><span><strong>Priority:</strong> {task_priority}</span></p><p><strong>Status:</strong> {task_status}<span><br><br></span><span>You can view the task on the following link: <a href=\"{task_url}\" data-mce-href=\"{task_url}\" style=\"\">view</a></span><br><br><span>Kind Regards,</span></p><p><strong>{name}</strong>,<br><span>{email_signature}</span><br></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'task', 'task_comments', 'New Comment on Task - {task_name}', '<p>Dear {staffname},<br><br>A comment has been made on the following task:<br><br><strong>Task:</strong> {task_name}<br><strong>Comment:</strong> {task_comment}<br><br>You can view the task on the following link: <a href=\"{task_url}\" data-mce-href=\"{task_url}\">view</a><br><br>Kind Regards,</p><p><strong>{name}</strong>,<br>{email_signature}<br></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'task', 'task_attachment', 'New Attachment on Task - {task_name}', '<p>Hi {staffname},<br><br><strong>{logged_in_user}</strong> added an attachment on the following task:<br><br><strong>Name:</strong> {task_name}<br><br>You can view the task on the following link: <a href=\"{task_url}\" data-mce-href=\"{task_url}\">view</a><br><br>Kind Regards,</p><p><strong>{name}</strong>,<br>{email_signature}<br></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'task', 'task_updated', 'Task Status Changed', '<p><span>Hi {staffname},</span><br><br><span><strong>{</strong></span><span><b>logged_in_user}</b> marked task as <strong>{task_status}</strong></span><br><br><span><strong>Name:</strong> {task_name}</span><br><span><strong>Due date:</strong> {task_duedate}</span><br><br><span>You can view the task on the following link: <a href=\"{task_url}\" data-mce-href=\"{task_url}\">{task_name}</a></span><br><br><span>Kind Regards,</span></p><p><strong>{name}</strong>,<br><span>{email_signature}</span><br></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'ticket', 'new_ticket', 'New Ticket Opened', '<p><span>Hi {customer},</span><br><br><span>New ticket has been opened.</span><br><br><span><strong>Subject:</strong> {ticket_subject}</span><br><span><strong>Department:</strong> {ticket_department}</span><br><span><strong>Priority:</strong> {ticket_priority}</span><br><br><span><strong>Ticket message:</strong></span><br><span>{ticket_message}</span><br><span><a><br></a>Kind Regards,</span></p><p><strong>{name}</strong>,<br><span>{email_signature}</span><br></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'ticket', 'new_customer_ticket', 'New Ticket Created', '<p><span>A new ticket has been created.</span><br><br><span><strong>Subject</strong>: {ticket_subject}</span><br><span><strong>Department</strong>: {ticket_department}</span><br><span><strong>Priority</strong>: {ticket_priority}</span><br><br><span><strong>Ticket message:</strong></span><br><span>{ticket_message}</span><br><br><span>Kind Regards,</span></p><p><strong>{name}</strong>,<br><span>{email_signature}</span><br></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'ticket', 'ticket_assigned', 'New ticket has been assigned to you', '<p><span>Hi {assigned},</span></p><p><span>A new support ticket&ampampnbsphas been assigned to you.</span></p><p><br><strong>Subject:</strong> {ticket_subject}<br><strong>Department:</strong> {ticket_department}<br><strong>Priority:</strong> {ticket_priority}<br></p><p><strong>Customer:</strong> {customer}</p><p><br><strong>Ticket message:</strong><br>{ticket_message}<br><br><a><br></a>Kind Regards,</p><p><strong>{name}</strong>,<br>{email_signature}</p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'ticket', 'ticket_reply_to_staff', 'Ticket Reply', '<p><span>A new support ticket reply from {customer}</span><br><br><span><strong>Subject</strong>: {ticket_subject}</span><br><span><strong>Department</strong>: {ticket_department}</span><br><span><strong>Priority</strong>: {ticket_priority}</span><br><br><span><strong>Ticket message:</strong></span><br><span>{ticket_message}</span><br><br><br><span>Kind Regards,</span></p><p><strong>{name}</strong>,<br><span>{email_signature}</span><br></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'ticket', 'ticket_autoresponse', 'New Ticket Opened', '<p><span>Hi {customer},</span><br><br><span>Thank you for contacting our team. A ticket has now been created for your request. </span></p><p><span>You will be notified when a response is made by email.</span><br><br><span><strong>Subject:</strong> {ticket_subject}</span><br><span><strong>Department</strong>: {ticket_department}</span><br><span><strong>Priority:</strong> {ticket_priority}</span><br><br><span><strong>Ticket message:</strong></span><br><span>{ticket_message}</span><br><br><br><span>Kind Regards,</span></p><p><strong>{name}</strong>,<br><span>{email_signature}</span><br></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'ticket', 'ticket_reply_to_customer', 'New Ticket Reply', '<p><span>Hi {customer},</span><br><br><span>You have a new ticket reply to ticket.</span><br><br></p><p><strong>Subject: </strong>{ticket_subject}<br><strong></strong><br><strong>Ticket message:</strong><br>{ticket_message}<br><br><a><br></a>Kind Regards,</p><p><strong>{name}</strong>,<br>{email_signature}</p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'proposal', 'send_proposal', 'Proposal With Number {proposal_number} Created', '<p>Dear {proposal_to},<br><br>Please find our attached proposal.<br><br>This proposal is valid until: {open_till}</p><p><br><strong>Proposal Subject:</strong> {subject}</p><p><strong>Proposal Details:</strong></p><p>{details}</p><p><br>Please don\'t hesitate to comment online if you have any questions.<br><br>We look forward to your communication.<br><br>Kind Regards,</p><p><strong>{name}</strong>,<br>{email_signature}<br></p>', 'Ciuis CRM', 1, 1, 1, NULL),
        (NULL, '$company' 'proposal', 'thankyou_email', 'Thank for you accepting the proposal', '<p>Dear {proposal_to},<br><br>Thank for for accepting the proposal.<br><br>We look forward to doing business with you.<br><br>We will contact you as soon as possible<br><br>Kind Regards,</p><p><strong>{name}</strong>,<br>{email_signature}<br></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'proposal', 'customer_accepted_proposal', 'Customer Accepted Proposal', '<div>Hi,<br><br>Client <strong>{proposal_to}</strong> accepted the following proposal:<br><br><strong>Number:</strong> {proposal_number}<br><strong>Subject</strong>: {subject}<br><strong>Total</strong>: {proposal_total}<br><br>Kind Regards,</div><div><strong>{name}</strong>,<br>{email_signature}</div>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'proposal', 'customer_rejected_proposal', 'Client Declined Proposal', '<div>Hi,<br><br>Client <strong>{proposal_to}</strong> declined the proposal <strong>{subject}</strong><br><br><strong>Proposal Number:</strong> {proposal_number}<br><strong>Total</strong>: {proposal_total}<br><br>Kind Regards,</div><div><strong>{name}</strong>,<br>{email_signature}</div><div> </div><div> </div><p> <br></p><div> </div>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'lead', 'lead_assigned', 'New lead assigned to you', '<p>Hello {lead_assigned_staff},<br><br>New lead is assigned to you.<br><br>Lead Name: {lead_name}<br>Lead Email: {lead_email}<br><br>You can view the lead on the following link: <a href=\"{lead_url}\" data-mce-href=\"{lead_url}\">View</a><br><br>Kind Regards,</p><p><strong>{name},</strong><br>{email_signature}<br></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'project', 'project_notification', 'New project created', '<p>Hello {customer},</p><p>New project is assigned to your company.<br><br><strong>Project Name:</strong> {project_name}<br><strong>Project Start Date:</strong> {project_start_date}</p><p>You can view the project on the following link: <a href=\"{project_url}\" data-mce-href=\"{project_url}\" style=\"\">{project_name}</a></p><p>\n\n\n</p><p>We are looking forward hearing from you.<br><br>Kind Regards,</p><p><strong>{name}</strong>,<br>{email_signature}</p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'project', 'staff_added', 'New project assigned to you', '<p>Hi {staff},<br><br>New project has been assigned to you.<br><br>You can view the project on the following link <a href=\"{project_url}\" data-mce-href=\"{project_url}\">{project_name}</a><br></p><p><br>Kind Regards,</p><p><strong>{name}</strong>,<br>{email_signature}</p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'project', 'new_file_uploaded_to_members', 'New Project File Uploaded - {project_name}', '<p>Hello {staff},</p><p>New project file is uploaded on <strong>{project_name}</strong> by <strong>{loggedin_staff}</strong>.</p><p>You can view the project on the following link: <a href=\"{project_url}\" data-mce-href=\"{project_url}\">{project_name}</a></p><p><br>Kind Regards,</p><p><strong>{name}</strong>,<br>{email_signature}</p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'project', 'new_file_uploaded_to_customer', 'New Project File Uploaded - {project_name}', '<p>Hello {customer},</p><p>New project file is uploaded on <strong>{project_name}</strong> by <strong>{loggedin_staff}</strong>.</p><p>You can view the project on the following link: <a href=\"http://localhost:8080/ciuiscrm/emails/template/%7Bproject_url%7D\" data-mce-href=\"{project_url}\">{project_name}</a></p><p><br>Kind Regards,</p><p><strong>{name}</strong>,<br>{email_signature}</p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'project', 'new_note_to_members', 'New Note on Project - {project_name}', '<p>Hello {staff},<br><br>New note has been made on project <strong>{project_name}</strong> by <strong>{loggedin_staff}</strong><br><br><strong>Note</strong>:</p><p><span>{note}</span><br><br>You can view the note on the following link: <a href=\"http://localhost:8080/ciuiscrm/emails/template/%7Bproject_url%7D\" data-mce-href=\"{project_url}\">{project_name}</a><br><br>Kind Regards,</p><p><strong>{name}</strong>,<br>{email_signature}</p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'project', 'new_note_to_customers', 'New Note on Project - {project_name}', '<p><span>Hello {customer},</span><br><br><span>New note has been made on project <strong>{project_name}</strong> by <strong>{loggedin_staff}</strong></span><br><br><span><strong>Note</strong>: </span></p><p><span>{note}</span><br><br><span>You can view the note on the following link: <a href=\"{project_url}\" data-mce-href=\"{project_url}\">{project_name}</a></span><br><br><span>Kind Regards,</span></p><p><strong>{name}</strong>,<br><span>{email_signature}</span></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'project', 'project_status_changed', 'Project Status Changed', '<p><span>Hi {customer},</span><br><br><span><strong>{loggedin_staff}</strong> marked project as <strong>{project_status}</strong></span><br><br></p><p><strong>Project Name: </strong>{project_name}<br><strong>Project End Date:</strong> {project_end_date}</p><p>You can view the project on the following link: <a href=\"http://localhost:8080/ciuiscrm/emails/template/%7Bproject_url%7D\" data-mce-href=\"{project_url}\">{project_name}</a></p><p><br><span>Kind Regards,</span></p><p><strong>{name}</strong>,<br><span>{email_signature}</span><br></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'expense', 'expense_created', 'Expense Created - {expense_number}', '<p><strong></strong>Hello {customer},<br><br>We have prepared the following expense for you: # <strong>{expense_number}</strong><br></p><p><strong>Expense Title:</strong></p><p><span>{expense_title}</span></p><p><strong>Expense Category:</strong></p><p><span>{expense_category}</span></p><p><strong>Expense Date: </strong>{expense_date}</p><p><strong>Expense Description:</strong></p><p><span>{expense_description}</span></p><p><br></p><p><strong>Expense Amount: </strong>{expense_amount}<br><br>Please contact us for more information.<br><br>Kind Regards,</p><p><strong>{name},</strong><br>{email_signature}</p>', 'Ciuis CRM', 1, 1, 1, NULL),
        (NULL, '$company' 'staff', 'customer_forgot_password', 'Reset password', '<h2><span>Reset password</span></h2><p>Hi {customer},</p><p><br>Forgot your password?<br>To create a new password, just follow this link:<br><br><a href=\"{password_url}\" data-mce-href=\"{password_url}\">Reset Password</a><br><br>You received this email, because it was requested by a user. This is part of the procedure to create a new password on the system. If you DID NOT request a new password then please ignore this email and your password will remain the same. <br></p><p>Regards,</p><p>{email_signature}<br></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'staff', 'customer_password_reset', 'Your password has been changed', '<p><span><strong>You have changed your password.<br></strong></span><br>Please, keep it in your records so you don\'t forget it.<br><br>Your email address for login is: <span style=\"color: rgb(0, 0, 255)\">{email}</span><br><br>If this was not you, please contact us.<br></p><p>Regards,<br>{email_signature}<br></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'invoice', 'invoice_recurring', 'Recurring Invoice with number {invoice_number} created', '<p><span><strong>INVOICE {invoice_number}</strong></span><br><br></p><div>Hello {customer},</div><div><br></div><div>We have prepared the following invoice for you: # <strong>{invoice_number}</strong></div><div><br></div><div>Invoice status: <strong>{invoice_status}</strong></div><div><br></div><div>You can view the invoice on the following link: <a href=\"{invoice_link}\" data-mce-href=\"{invoice_link}\">{invoice_number}</a></div><div><br></div><div>Please contact us for more information.</div><div><br></div><div>Kind Regards,</div><div><br></div><div><strong>{name},</strong></div><div>{email_signature}</div>', 'Ciuis CRM', 1, 1, 1, NULL),
        (NULL, '$company' 'lead', 'web_lead_created', 'Leads - E-mail #1', '<p>Olá {lead_name}!</p><p>Conforme falado via Linkedin com nosso CEO, Sandro Filipe, anexamos aqui a apresentação da nossa plataforma Discovery Dados para sua análise.</p><p><br>Conseguimos agendar uma call para falarmos sobre os detalhes da nossa tecnologia? <br><br>Atenciosamente,,</p><p><strong>{name},</strong><br>{email_signature}</p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'lead', 'lead_submitted', 'Leads - E-mails #2', '<p>Olá {lead_name}, tudo bem?<br><br>Chegou a analisar nossa apresentação?<br><br>Conseguimos dar sequencia com vocês?<br><br><br>Atenciosamente,</p><p>,<br><strong>{email_signature}</strong><br></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'expense', 'expense_recurring', 'Recurring Expense with number {expense_number} created', '<p>Hello {customer},</p><p><br>We have prepared the following expense for you: # <strong>{expense_number}</strong><br></p><p><strong>Expense Title:</strong></p><p>{expense_title}</p><p><strong>Expense Category:</strong></p><p>{expense_category}</p><p><strong>Expense Date: </strong>{expense_date}</p><p><strong>Expense Amount: </strong>{expense_amount}<br><br>Please contact us for more information.<br><br>Kind Regards,</p><p><strong>{name},</strong><br>{email_signature}</p>', 'Ciuis CRM', 0, 1, 1, NULL),
        (NULL, '$company' 'project', 'new_note_to_members_by_customer', 'New Note on Project by customer - {project_name}', '<p>Hello,<br><br>New note has been made on project <strong>{project_name}</strong> by <strong>{loggedin_staff}</strong><br><br><strong>Note</strong>:</p><p><span>{note}</span><br><br>Kind Regards,</p><p><strong>{name}</strong>,<br>{email_signature}</p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'project', 'new_file_uploaded_by_customer', 'New Project File Uploaded By Customer - {project_name}', '<p>Hello,</p><p>New project file is uploaded on <strong>{project_name}</strong> by <strong>{loggedin_staff}</strong>.</p><p>You can find the attachment below attached.</p><p><br>Kind Regards,</p><p><strong>{name}</strong>,<br>{email_signature}</p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'order', 'order_message', 'Order {order_number} confirmed', '<p>Hello {customer},<br></p><div><br></div><div><br></div><div>Thank you very much for your recent order with {company_name}. Your order is currently being processed and you will receive a shipping confirmation once the order has been shipped.</div><div><br></div><div><br></div><div>Please contact us for more information.</div><div><br></div><div>Kind Regards,</div><div><br></div><div><strong>{name},</strong></div><div>{email_signature}</div>', 'Ciuis CRM', 1, 1, 1, NULL),
        (NULL, '$company' 'project', 'new_note_to_members_by_customer', 'New Note on Project by customer - {project_name}', '<p>Hello,<br><br>New note has been made on project <strong>{project_name}</strong> by <strong>{loggedin_staff}</strong><br><br><strong>Note</strong>:</p><p><span>{note}</span><br><br>Kind Regards,</p><p><strong>{name}</strong>,<br>{email_signature}</p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'project', 'new_file_uploaded_by_customer', 'New Project File Uploaded By Customer - {project_name}', '<p>Hello,</p><p>New project file is uploaded on <strong>{project_name}</strong> by <strong>{loggedin_staff}</strong>.</p><p>You can find the attachment below attached.</p><p><br>Kind Regards,</p><p><strong>{name}</strong>,<br>{email_signature}</p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'quote', 'request_quote', 'New Quote requested by {customer_name}', '<p>Dear {staff},<br><br>{customer_name} has requested a new quote.<br><br><br><strong>Quote Subject:</strong> {subject}</p><p><strong>Quote </strong><strong>Details:</strong></p><p>{details}</p><p><strong>Quote </strong><strong>Link: </strong><a href=\"{quote_link}\" data-mce-href=\"{quote_link}\">{quote_link}</a></p><p><br><br>Kind Regards,</p><p><strong>{company_name}</strong>,<br>{company_email}<br></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'quote', 'quote_status_changed', 'Quote Status has Changed by {staff}', '<div><p>Hi {customer_name},<br><br><strong>{staff}</strong> marked quote as <strong>{quote_status}</strong><br><br></p><p><strong>Quote Subject: </strong>{subject}<br><strong>Quote Details:</strong></p><p>{details}</p><p><br>Kind Regards,</p><p><strong>{company_name}</strong>,<br>{company_email}</p></div><div><br></div>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'events', 'event_reminder', 'Event Reminder', '<p>Hello {staff},<br></p><p>This is the {event_title} reminder.</p><p><br data-mce-bogus=\"1\"></p><p>Regards,</p><p><strong>{company_name}</strong></p><p>{company_email}</p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'purchase', 'purchase_message', 'purchase with number {purchase_number} created', '<p><span><strong>PURCHASE {purchase_number}</strong></span><br><br></p><div>Hello {vendor_name},</div><div><br></div><div>We have prepared the following purchase order for you: # <strong>{purchase_number}</strong></div><div><br></div><div>Purchase status: <strong>{purchase_status}</strong></div><div><br></div><div>You can view the purchase order on the following link: <a href=\"{purchase_link}\" data-mce-href=\"{purchase_link}\">{purchase_number}</a></div><div><br></div><div>Please contact us for more information.</div><div><br></div><div>Kind Regards,</div><div><br></div><div><strong>{company_name},</strong></div><div>{company_email}</div>', 'Ciuis CRM', 1, 1, 1, NULL),
        (NULL, '$company' 'purchase', 'purchase_payment', 'Purchase Payment Recorded', '<p><span>Hello {vendor_name}<br><br></span>Thank you for the payment. Find the payment details below:<br><br>-------------------------------------------------<br><br>Amount: <strong>{payment_amount}<br></strong>Date: <strong>{payment_date}</strong><br>Purchase number: <span><strong># {purchase_number}<br><br></strong></span>-------------------------------------------------<br><br>You can always view the purchase for this payment at the following link:{purchase_link}</p><p><a>View Purchase</a><br><br>We are looking forward working with you.<br><br><span>Kind Regards,</span></p><p><strong>{company_name}</strong><br><span>{company_email}</span></p>', 'Ciuis CRM', 1, 1, 0, NULL),
        (NULL, '$company' 'purchase', 'purchase_recurring', 'Recurring Purchase with number {purchase_number} created', '<p><span><strong>PURCHASE {purchase_number}</strong></span><br><br></p><div>Hello {vendor_name},</div><div><br></div><div>We have prepared the following purchase for you: # <strong>{purchase_number}</strong></div><div><br></div><div>Purchase status: <strong>{purchase_status}</strong></div><div><br></div><div>You can view the purchase on the following link: {purchase_link}</div><div><br></div><div>Please contact us for more information.</div><div><br></div><div>Kind Regards,</div><div><br></div><div><strong>{company_name},</strong></div><div>{company_email}</div>', 'Ciuis CRM', 1, 1, 1, NULL),
        (NULL, '$company' 'deposit', 'deposit_message', 'Deposit with number {deposit_number} created', '<p><span><strong>DEPOSIT {deposit_number}</strong></span><br><br></p><div>Hello {customer_name},</div><div><br></div><div>We have prepared the following deposit for you: # <strong>{deposit_number}</strong></div><div><br></div><div>Deposit status: <strong>{deposit_status}</strong></div><div><br></div><div>You can view the deposit on the following link: <strong>{deposit_link}</strong></div><div><br></div><div>Please contact us for more information.</div><div><br></div><div>Kind Regards,</div><div><br></div><div><strong>{company_name},</strong></div><div>{company_email}</div>', 'Ciuis CRM', 1, 1, 1, NULL),
        (NULL, '$company' 'deposit', 'recurring_deposit', 'Recurring Deposit with number {deposit_number} created', '<p><strong>DEPOSIT {deposit_number}</strong><br><br></p><div>Hello {customer_name},</div><div><br></div><div>We have prepared the following deposit for you: # <strong>{deposit_number}</strong></div><div><br></div><div>Deposit status: <strong>{deposit_status}</strong></div><div><br></div><div>You can view the deposit on the following link: {deposit_link}</div><div><br></div><div>Please contact us for more information.</div><div><br></div><div>Kind Regards,</div><div><br></div><div><strong>{company_name},</strong></div><div>{company_email}</div>', 'Ciuis CRM', 1, 1, 1, NULL),
        (NULL, '$company' 'appointment', 'new_appointment', 'New Appointment', '<p>Hello {staff_name},<br></p><div>{contact_name} has requested for appointment.<br></div><div><br></div><div>Appointment Date: <strong>{appointment_date}</strong><br></div><div>Appointment Time: <strong>{appointment_time}</strong><strong><br data-mce-bogus=\"1\"></strong></div><div><strong><br data-mce-bogus=\"1\"></strong></div><div>Please contact us for more information.<br></div><div><br></div><div>Kind Regards,</div><div><br></div><div><strong>{company_name},</strong></div><div>{company_email}</div>', 'Ciuis CRM', 1, 1, 1, NULL),
        (NULL, '$company' NULL, NULL, NULL, '<p>teste assinatura</p>', NULL, 1, 1, 0, NULL);");
    }


    function insertCategorias($company)
    {
        $this->db->query("INSERT INTO `expensecat` (`id`, `id_company`, `ctg_system`, `name`, `description`, `id_vendor`) 
        VALUES 
        (NULL, '$company', '1', 'Salário', 'Salário', NULL),
        (NULL, '$company', '2', 'Comissão', 'Comissão', NULL),
        (NULL, '$company', '3', 'Bonus', 'Bonus', NULL)
        ");
    }



    function insertFunilPadrao($company)
    {
        $builder = $this->db->table('leads_list');
        $builder->insert(array(
            'id_company' => $company,
            'nm_list' => 'Padrão'
        ));
        $id_list = $this->db->insertID();

        $builder = $this->db->table('leadsstatus');
        $builder->insertBatch(array(
            array(
                'id_list' => $id_list,
                'ordem' => '1',
                'name' => 'Novas Oportunidades',
                'color' => NULL
            ),
            array(
                'id_list' => $id_list,
                'ordem' => '2',
                'name' => 'Apresentação',
                'color' => NULL
            ),
            array(
                'id_list' => $id_list,
                'ordem' => '3',
                'name' => 'Call',
                'color' => NULL
            ),
            array(
                'id_list' => $id_list,
                'ordem' => '4',
                'name' => 'Negociação',
                'color' => NULL
            ),
            array(
                'id_list' => $id_list,
                'ordem' => '5',
                'name' => 'Pipeline',
                'color' => NULL
            )
        ));
    }


    function insertAssinatura($id_company)
    {
        $builder = $this->db->table('email_assinaturas');
        $builder->where('id_company', '1');
        $data = $builder->get()->getRowArray();

        $params = array(
            'id_company' => $id_company,
            'message' => $data["message"],
        );
        $builder->insert($params);
    }

    function insertAviso($id_company)
    {
        $builder = $this->db->table('avisos');
        $builder->where('id_company', '1');
        $builder->where('is_ativo', '1');
        $avisos = $builder->get()->getResultArray();

        foreach ($avisos as $aviso) {
            $params = array(
                'id_company' => $id_company,
                'nm_aviso' => $aviso["nm_aviso"],
                'tempo' => $aviso["tempo"],
                'tipo' => $aviso["tipo"],
                'anexo' => $aviso["anexo"],
                'opcaoAlerta' => $aviso["opcaoAlerta"],
                'frequencia' => $aviso["frequencia"],
                'diaSemana' => $aviso["diaSemana"],
                'diaMes' => $aviso["diaMes"],
                'menssagem' => $aviso["menssagem"],
                'ultima_atualizacao' => $aviso["ultima_atualizacao"],
                'is_ativo' => '1',
                'created' => date('Y-m-d H:i:s'),
            );
            $builder->insert($params);
        }
    }
}
