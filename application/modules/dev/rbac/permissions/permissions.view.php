<?php
namespace Modules\Dev\Rbac;

use Modules\Dev\RbacView;

final class PermissionsView extends RbacView
{
    protected function executeIndexaction()
    {
        $sTemplateFile = 'rbac/add_permission';

        /** Load rbac role tree */
        $this->presentationObjectFactory
        ->build('rbac_role_tree', true)
        ->assignData();

        /** Load rbac permission tree */
        $this->presentationObjectFactory
        ->build('rbac_permission_tree', true)
        ->assignData();

        /** Load required scripts */
        $this->presentationObjectFactory
        ->build('scripttags', true)
        ->assignData($sTemplateFile);

        $this->display('custom/header.htm');
        $this->display('custom/sidebar.htm');
        $this->display('custom/rightbar.htm');
        $this->display('custom/' . $sTemplateFile . '.htm');
        $this->display('custom/footer.htm');
    }

	protected function executeAddrbacpermissions()
	{

	}
}