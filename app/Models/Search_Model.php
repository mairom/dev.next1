<?php

namespace App\Models;

use CodeIgniter\Model;

class Search_Model extends Model
{

    function search_data($input)
    {
        $input = trim($input);
        $isAdmin = $this->isAdmin();
        $limit = 5;
        $result = [];

        $staff_search = $this->searchStaffs($input, $limit);
        if (count($staff_search['result']) > 0) {
            $result[] = $staff_search;
        }

        $contacts_search = $this->searchCustomers($input, $limit);
        if (count($contacts_search['result']) > 0) {
            $result[] = $contacts_search;
        }

        $tickets_search = $this->searchTickets($input, $limit);
        if (count($tickets_search['result']) > 0) {
            $result[] = $tickets_search;
        }

        $leads_search = $this->searchLeads($input, $limit);
        if (count($leads_search['result']) > 0) {
            $result[] = $leads_search;
        }

        $proposal_search = $this->searchProposals($input, $limit);
        if (count($proposal_search['result']) > 0) {
            $result[] = $proposal_search;
        }

        $invoices_search = $this->searchInvoices($input, $limit);
        if (count($invoices_search['result']) > 0) {
            $result[] = $invoices_search;
        }

        $expenses_search = $this->searchExpenses($input, $limit);
        if (count($expenses_search['result']) > 0) {
            $result[] = $expenses_search;
        }

        $projects_search = $this->searchProjects($input, $limit);
        if (count($projects_search['result']) > 0) {
            $result[] = $projects_search;
        }

        $products_search = $this->searchProducts($input, $limit);
        if (count($products_search['result']) > 0) {
            $result[] = $products_search;
        }

        $orders_search = $this->searchOrders($input, $limit);
        if (count($orders_search['result']) > 0) {
            $result[] = $orders_search;
        }

        // $staff_search = $this->_search_accounts($input, $limit);
        // if (count($staff_search['result']) > 0) {
        //     $result[] = $staff_search;
        // }

        $tasks_search = $this->searchTasks($input, $limit);
        if (count($tasks_search['result']) > 0) {
            $result[] = $tasks_search;
        }

        $vendors_search = $this->searchVendors($input, $limit);
        if (count($vendors_search['result']) > 0) {
            $result[] = $vendors_search;
        }

        $purchases_search = $this->searchPurchases($input, $limit);
        if (count($purchases_search['result']) > 0) {
            $result[] = $purchases_search;
        }

        $deposits_search = $this->searchDeposits($input, $limit);
        if (count($deposits_search['result']) > 0) {
            $result[] = $deposits_search;
        }

        return $result;
    }

    function searchStaffs($q, $limit)
    {
        $result = [
            'result' => [],
            'type' => 'staff',
        ];
        $isAdmin = $this->isAdmin();
        $user_id = session()->get('usr_id');
        $has_permission_view_inovices = $this->has_permission('staff');

        if ($isAdmin && $has_permission_view_inovices) {
            $builder = $this->db->table('staff');
            $builder->select('staffname as name, staff.id as staff_id, email, staff.staff_number');
            $builder->join('departments', 'staff.department_id = departments.id', 'left');
            $builder->like('staff.id', $q, 'both');
            $builder->orLike('staff_number', $q, 'both');
            $builder->orLike('email', $q, 'both');
            $builder->orLike('staffname', $q, 'both');
            $builder->orLike('address', $q, 'both');
            $builder->orLike('departments.name', $q, 'both');
            $builder->orderBy('staff.id', 'desc');
            $builder->limit($limit);
            $result['result'] = $builder->get()->getResultArray();
        }
        return $result;
    }

    function searchTasks($q, $limit)
    {
        $result = [
            'result' => [],
            'type' => 'tasks',
        ];
        $isAdmin = $this->isAdmin();
        $user_id = session()->get('usr_id');
        $has_permission_view_tasks = $this->has_permission('tasks');

        if ($has_permission_view_tasks) {
            $builder = $this->db->table('tasks');
            $builder->select('staff.staffname as staff, tasks.id, tasks.name, tasks.task_number');
            $builder->join('staff', 'tasks.assigned = staff.id', 'left');
            $builder->like('tasks.id', $q, 'both');
            $builder->orLike('tasks.task_number', $q, 'both');
            $builder->orLike('staff.email', $q, 'both');
            $builder->orLike('staff.staffname', $q, 'both');
            $builder->orLike('tasks.name', $q, 'both');
            $builder->orLike('tasks.description', $q, 'both');
            $builder->orderBy('tasks.id', 'desc');
            $builder->limit($limit);
            $result['result'] = $builder->get()->getResultArray();
        }
        return $result;
    }

    function searchOrders($q, $limit)
    {
        $result = [
            'result' => [],
            'type' => 'orders',
        ];
        $isAdmin = $this->isAdmin();
        $user_id = session()->get('usr_id');
        $has_permission_view_orders = $this->has_permission('orders');

        if ($has_permission_view_orders) {
            $builder = $this->db->table('orders');
            $builder->select('staff.staffname as staff, orders.id, orders.subject, customers.namesurname as name, customers.company as company, orders.order_number');
            $builder->join('staff', 'orders.assigned = staff.id', 'left');
            $builder->join('customers', "orders.relation = customers.id AND relation_type = 'customer'", 'left');
            $builder->like('orders.id', $q, 'both');
            $builder->orLike('staff.email', $q, 'both');
            $builder->orLike('staff.staffname', $q, 'both');
            $builder->orLike('orders.subject', $q, 'both');
            $builder->orLike('orders.content', $q, 'both');
            $builder->orLike('customers.zipcode', $q, 'both');
            $builder->orLike('customers.state', $q, 'both');
            $builder->orLike('customers.city', $q, 'both');
            $builder->orLike('customers.address', $q, 'both');
            $builder->orLike('customers.email', $q, 'both');
            $builder->orLike('customers.phone', $q, 'both');
            $builder->orLike('customers.namesurname', $q, 'both');
            $builder->orLike('orders.order_number', $q, 'both');
            $builder->orderBy('orders.id', 'desc');
            $builder->limit($limit);
            $result['result'] = $builder->get()->getResultArray();
        }
        return $result;
    }

    function searchTickets($q, $limit)
    {
        $result = [
            'result' => [],
            'type' => 'tickets',
        ];
        $isAdmin = $this->isAdmin();
        $user_id = session()->get('usr_id');
        $has_permission_view_tickets = $this->has_permission('tickets');

        if ($has_permission_view_tickets) {
            $builder = $this->db->table('tickets');
            $builder->select('tickets.id, tickets.subject, tickets.message, tickets.ticket_number');
            $builder->join('departments', 'tickets.department_id = departments.id', 'left');
            $builder->join('staff', 'tickets.staff_id = staff.id', 'left');
            $builder->groupStart()
                ->like('tickets.id', $q, 'after')
                ->orLike('staff.staffname', $q)
                ->orLike('staff.email', $q)
                ->orLike('departments.name', $q)
                ->orLike('tickets.subject', $q)
                ->orLike('tickets.message', $q)
                ->orLike('tickets.ticket_number', $q)
                ->groupEnd();
            $builder->orderBy('tickets.date', 'desc');
            $builder->orderBy('tickets.priority', 'desc');
            $builder->limit($limit);
            $result['result'] = $builder->get()->getResultArray();
        }
        return $result;
    }

    function searchCustomers($q, $limit)
    {
        $result = [
            'result' => [],
            'type' => 'customers',
        ];
        $isAdmin = $this->isAdmin();
        $user_id = session()->get('usr_id');
        $has_permission_view_customers = $this->has_permission('customers');

        if ($has_permission_view_customers) {
            $builder = $this->db->table('customers');
            $builder->select('customers.zipcode, customers.customer_number, customers.state, customers.city, customers.address, customers.email, customers.phone, customers.namesurname as name, customers.company, customers.id');
            $builder->join('customergroups', 'customers.groupid = customergroups.id', 'left');
            if (!$isAdmin) {
                $builder->where('customers.staff_id', $user_id);
            }
            $builder->groupStart()
                ->like('customers.id', $q, 'after')
                ->orLike('customers.zipcode', $q)
                ->orLike('customers.state', $q)
                ->orLike('customers.city', $q)
                ->orLike('customers.address', $q)
                ->orLike('customers.email', $q)
                ->orLike('customers.phone', $q)
                ->orLike('customers.company', $q)
                ->orLike('customers.namesurname', $q)
                ->orLike('customers.customer_number', $q)
                ->orLike('customergroups.name', $q)
                ->groupEnd();
            $builder->orderBy('customers.id', 'desc');
            $builder->limit($limit);
            $result['result'] = $builder->get()->getResultArray();
        }
        return $result;
    }

    function searchLeads($q, $limit)
    {
        $result = [
            'result' => [],
            'type' => 'leads',
        ];
        $isAdmin = $this->isAdmin();
        $user_id = session()->get('usr_id');
        $has_permission_view_leads = $this->has_permission('leads');

        if ($has_permission_view_leads) {
            $builder = $this->db->table('leads');
            $builder->select('leads.company, leads.name, leads.title, leads.email, leads.id, leads.lead_number');
            $builder->join('leadsstatus', 'leads.status = leadsstatus.id', 'left');
            $builder->join('leadssources', 'leads.source = leadssources.id', 'left');
            $builder->join('staff', 'leads.assigned_id = staff.id', 'left');
            if (!$isAdmin) {
                $builder->groupStart()
                    ->where('public', 1)
                    ->orWhere('leads.assigned_id', $user_id)
                    ->orWhere('staff_id', $user_id)
                    ->groupEnd();
            }
            $builder->groupStart()
                ->like('leads.id', $q, 'after')
                ->orLike('staff.email', $q)
                ->orLike('staff.staffname', $q)
                ->orLike('leads.name', $q)
                ->orLike('leads.title', $q)
                ->orLike('leads.zip', $q)
                ->orLike('leads.state', $q)
                ->orLike('leads.city', $q)
                ->orLike('leads.address', $q)
                ->orLike('leads.email', $q)
                ->orLike('leads.phone', $q)
                ->orLike('leads.description', $q)
                ->orLike('leads.company', $q)
                ->orLike('leadsstatus.name', $q)
                ->orLike('leadssources.name', $q)
                ->orLike('leads.lead_number', $q)
                ->groupEnd();
            $builder->orderBy('leads.id', 'desc');
            $builder->limit($limit);
            $result['result'] = $builder->get()->getResultArray();
        }
        return $result;
    }

    function searchProducts($q, $limit)
    {
        $result = [
            'result' => [],
            'type' => 'products',
        ];
        $isAdmin = $this->isAdmin();
        $user_id = session()->get('usr_id');
        $has_permission_view_products = $this->has_permission('products');

        if ($has_permission_view_products) {
            $builder = $this->db->table('products');
            $builder->select('products.id as id, products.productname as name, products.description, products.product_number');
            $builder->join('productcategories', 'products.categoryid = productcategories.id', 'left');
            $builder->groupStart()
                ->like('products.id', $q, 'after')
                ->orLike('products.productname', $q)
                ->orLike('products.description', $q)
                ->orLike('products.code', $q)
                ->orLike('productcategories.name', $q)
                ->orLike('products.product_number', $q)
                ->groupEnd();
            $builder->orderBy('products.id', 'desc');
            $builder->limit($limit);
            $result['result'] = $builder->get()->getResultArray();
        }
        return $result;
    }


    function searchExpenses($q, $limit)
    {
        $result = [
            'result' => [],
            'type' => 'expenses',
        ];
        $isAdmin = $this->isAdmin();
        $user_id = session()->get('usr_id');
        $has_permission_view_expenses = $this->has_permission('expenses');

        if ($has_permission_view_expenses) {
            $builder = $this->db->table('expenses');
            $builder->select('expenses.title, expenses.id, expenses.expense_number');
            $builder->join('customers', 'expenses.customer_id = customers.id', 'left');
            $builder->join('expensecat', 'expenses.category_id = expensecat.id', 'left');
            $builder->join('staff', 'expenses.staff_id = staff.id', 'left');
            if (!session()->get('other')) {
                if (!$isAdmin) {
                    $builder->where('expenses.staff_id', $user_id);
                }
            }
            $builder->groupStart()
                ->like('expenses.id', $q, 'after')
                ->orLike('staff.email', $q)
                ->orLike('staff.staffname', $q)
                ->orLike('expenses.description', $q)
                ->orLike('expenses.title', $q)
                ->orLike('customers.zipcode', $q)
                ->orLike('customers.state', $q)
                ->orLike('customers.city', $q)
                ->orLike('customers.address', $q)
                ->orLike('customers.email', $q)
                ->orLike('customers.phone', $q)
                ->orLike('customers.namesurname', $q)
                ->orLike('expensecat.name', $q)
                ->orLike('expenses.expense_number', $q)
                ->groupEnd();
            $builder->orderBy('expenses.id', 'desc');
            $builder->limit($limit);
            $result['result'] = $builder->get()->getResultArray();
        }
        return $result;
    }

    function searchProposals($q, $limit)
    {
        $result = [
            'result' => [],
            'type' => 'proposals',
        ];
        $isAdmin = $this->isAdmin();
        $user_id = session()->get('usr_id');
        $has_permission_view_proposals = $this->has_permission('proposals');

        if ($has_permission_view_proposals) {
            $builder = $this->db->table('proposals');
            $builder->select('staff.staffname as staffmembername, staff.email as staffemail, proposals.id as proposal_id, proposals.content, proposals.assigned as staffId, proposals.subject, customers.zipcode, customers.state, customers.city, customers.address, customers.email, customers.phone, customers.namesurname, proposals.total, proposals.proposal_number');
            $builder->join('staff', 'proposals.assigned = staff.id', 'left');
            $builder->join("customers", "proposals.relation = customers.id AND proposals.relation_type = 'customer'", 'left');
            if (!$isAdmin) {
                $builder->where('proposals.assigned', $user_id);
            }
            $builder->groupStart()
                ->like('proposals.id', $q, 'after')
                ->orLike('staff.email', $q)
                ->orLike('staff.staffname', $q)
                ->orLike('proposals.content', $q)
                ->orLike('proposals.subject', $q)
                ->orLike('customers.zipcode', $q)
                ->orLike('customers.state', $q)
                ->orLike('customers.city', $q)
                ->orLike('customers.address', $q)
                ->orLike('customers.email', $q)
                ->orLike('customers.phone', $q)
                ->orLike('customers.namesurname', $q)
                ->orLike('proposals.proposal_number', $q)
                ->groupEnd();
            $builder->orderBy('proposals.id', 'desc');
            $builder->limit($limit);
            $result['result'] = $builder->get()->getResultArray();
        }
        return $result;
    }

    function searchProjects($q, $limit)
    {
        $result = [
            'result' => [],
            'type' => 'projects',
        ];
        $isAdmin = $this->isAdmin();
        $user_id = session()->get('usr_id');
        $has_permission_view_projects = $this->has_permission('projects');

        if ($has_permission_view_projects) {
            $builder = $this->db->table('projects');
            $builder->select('projects.name, projects.status_id as status, projects.id, projects.project_number');
            $builder->join('customers', 'projects.customer_id = customers.id', 'left');
            $builder->groupStart()
                ->like('projects.id', $q, 'after')
                ->orLike('projects.name', $q)
                ->orLike('projects.description', $q)
                ->orLike('customers.zipcode', $q)
                ->orLike('customers.state', $q)
                ->orLike('customers.city', $q)
                ->orLike('customers.address', $q)
                ->orLike('customers.email', $q)
                ->orLike('customers.phone', $q)
                ->orLike('customers.namesurname', $q)
                ->orLike('projects.project_number', $q)
                ->groupEnd();
            $builder->orderBy('projects.id', 'desc');
            $builder->limit($limit);
            $result['result'] = $builder->get()->getResultArray();
        }
        return $result;
    }

    function searchInvoices($q, $limit)
    {
        $result = [
            'result' => [],
            'type' => 'invoices',
        ];
        $isAdmin = $this->isAdmin();
        $user_id = session()->get('usr_id');
        $has_permission_view_invoices = $this->has_permission('invoices');

        if ($has_permission_view_invoices) {
            $builder = $this->db->table('invoices');
            $builder->select('invoices.invoice_number, staff.staffname as staffmembername, staff.email as staffemail, invoices.id as invoice_id, invoices.staff_id as staffId, customers.zipcode, customers.state, customers.city, customers.address, customers.email, customers.phone, customers.namesurname, customers.company, invoices.total');
            $builder->join('customers', 'invoices.customer_id = customers.id', 'left');
            $builder->join('staff', 'invoices.staff_id = staff.id', 'left');
            if (!session()->get('other')) {
                if (!$isAdmin) {
                    $builder->where('invoices.staff_id', $user_id);
                }
            }
            $builder->groupStart()
                ->like('invoices.id', $q, 'after')
                ->orLike('staff.email', $q)
                ->orLike('staff.staffname', $q)
                ->orLike('customers.zipcode', $q)
                ->orLike('customers.state', $q)
                ->orLike('customers.city', $q)
                ->orLike('customers.address', $q)
                ->orLike('customers.email', $q)
                ->orLike('customers.phone', $q)
                ->orLike('customers.namesurname', $q)
                ->orLike('customers.company', $q)
                ->orLike('invoices.invoice_number', $q)
                ->groupEnd();
            $builder->orderBy('invoices.id', 'desc');
            $builder->limit($limit);
            $result['result'] = $builder->get()->getResultArray();
        }
        return $result;
    }

    function has_permission($path)
    {
        $relation = session()->get('usr_id');
        $builder = $this->db->table('privileges');
        $builder->select('*, permissions.key as permission_key');
        $builder->join('permissions', 'privileges.permission_id = permissions.id', 'left');
        $builder->where([
            'permissions.key' => $path,
            'privileges.relation' => $relation,
            'privileges.relation_type' => 'staff'
        ]);
        $rows = $builder->countAllResults();
        return $rows > 0;
    }

    function isAdmin()
    {
        $id = session()->get('usr_id');
        $builder = $this->db->table('staff');
        $builder->where(['admin' => 1, 'id' => $id]);
        $rows = $builder->countAllResults();
        return $rows > 0;
    }

    function searchVendors($q, $limit)
    {
        $result = [
            'result' => [],
            'type' => 'vendors',
        ];
        $isAdmin = $this->isAdmin();
        $user_id = session()->get('usr_id');
        $has_permission_view_vendors = $this->has_permission('vendors');

        if ($has_permission_view_vendors) {
            $builder = $this->db->table('vendors');
            $builder->select('vendors.zipcode, vendors.vendor_number, vendors.state, vendors.city, vendors.address, vendors.email, vendors.phone, vendors.company, vendors.id');
            $builder->join('vendors_groups', 'vendors.groupid = vendors_groups.id', 'left');
            if (!$isAdmin) {
                $builder->where('vendors.staff_id', $user_id);
            }
            $builder->groupStart()
                ->like('vendors.id', $q, 'after')
                ->orLike('vendors.zipcode', $q)
                ->orLike('vendors.state', $q)
                ->orLike('vendors.city', $q)
                ->orLike('vendors.address', $q)
                ->orLike('vendors.email', $q)
                ->orLike('vendors.phone', $q)
                ->orLike('vendors.company', $q)
                ->orLike('vendors.vendor_number', $q)
                ->orLike('vendors_groups.name', $q)
                ->groupEnd();
            $builder->orderBy('vendors.id', 'desc');
            $builder->limit($limit);
            $result['result'] = $builder->get()->getResultArray();
        }
        return $result;
    }

    function searchPurchases($q, $limit)
    {
        $result = [
            'result' => [],
            'type' => 'purchases',
        ];
        $isAdmin = $this->isAdmin();
        $user_id = session()->get('usr_id');
        $has_permission_view_purchases = $this->has_permission('purchases');

        if ($has_permission_view_purchases) {
            $builder = $this->db->table('purchases');
            $builder->select('purchases.purchase_number, purchases.id as purchase_id, vendors.zipcode, vendors.state, vendors.city, vendors.address, vendors.email, vendors.phone, vendors.company, purchases.total');
            $builder->join('vendors', 'purchases.vendor_id = vendors.id', 'left');
            if (!session()->get('other')) {
                if (!$isAdmin) {
                    $builder->where('purchases.staff_id', $user_id);
                }
            }
            $builder->groupStart()
                ->like('purchases.id', $q, 'after')
                ->orLike('vendors.zipcode', $q)
                ->orLike('vendors.state', $q)
                ->orLike('vendors.city', $q)
                ->orLike('vendors.address', $q)
                ->orLike('vendors.email', $q)
                ->orLike('vendors.phone', $q)
                ->orLike('vendors.company', $q)
                ->orLike('purchases.purchase_number', $q)
                ->groupEnd();
            $builder->orderBy('purchases.id', 'desc');
            $builder->limit($limit);
            $result['result'] = $builder->get()->getResultArray();
        }
        return $result;
    }

    function searchDeposits($q, $limit)
    {
        $result = [
            'result' => [],
            'type' => 'deposits',
        ];
        $isAdmin = $this->isAdmin();
        $user_id = session()->get('usr_id');
        $has_permission_view_deposits = $this->has_permission('deposits');

        if ($has_permission_view_deposits) {
            $builder = $this->db->table('deposits');
            $builder->select('deposits.title, deposits.id, deposits.deposit_number');
            $builder->join('customers', 'deposits.customer_id = customers.id', 'left');
            $builder->join('depositcat', 'deposits.category_id = depositcat.id', 'left');
            $builder->join('staff', 'deposits.staff_id = staff.id', 'left');
            if (!$isAdmin) {
                $builder->where('deposits.staff_id', $user_id);
            }
            $builder->groupStart()
                ->like('deposits.id', $q, 'after')
                ->orLike('staff.email', $q)
                ->orLike('staff.staffname', $q)
                ->orLike('deposits.description', $q)
                ->orLike('deposits.title', $q)
                ->orLike('customers.zipcode', $q)
                ->orLike('customers.state', $q)
                ->orLike('customers.city', $q)
                ->orLike('customers.address', $q)
                ->orLike('customers.email', $q)
                ->orLike('customers.phone', $q)
                ->orLike('customers.namesurname', $q)
                ->orLike('depositcat.name', $q)
                ->orLike('deposits.deposit_number', $q)
                ->groupEnd();
            $builder->orderBy('deposits.id', 'desc');
            $builder->limit($limit);
            $result['result'] = $builder->get()->getResultArray();
        }
        return $result;
    }
}
