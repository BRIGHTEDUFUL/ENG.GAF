<?php

$modules = ['Wing', 'Aircraft', 'MaintenanceTask', 'MaintenanceLog', 'FlightLog', 'Incident'];

$contractsDir = __DIR__ . '/app/Repositories/Contracts';
$repoDir = __DIR__ . '/app/Repositories';
$servicesDir = __DIR__ . '/app/Services';

if (!is_dir($contractsDir)) {
    mkdir($contractsDir, 0777, true);
}
if (!is_dir($servicesDir)) {
    mkdir($servicesDir, 0777, true);
}

foreach ($modules as $module) {
    // Interface
    $interfaceContent = "<?php\n\nnamespace App\\Repositories\\Contracts;\n\ninterface {$module}RepositoryInterface\n{\n    public function all();\n    public function find(\$id);\n    public function create(array \$data);\n    public function update(\$id, array \$data);\n    public function delete(\$id);\n}\n";
    file_put_contents("$contractsDir/{$module}RepositoryInterface.php", $interfaceContent);

    // Concrete class
    $classContent = "<?php\n\nnamespace App\\Repositories;\n\nuse App\\Models\\$module;\nuse App\\Repositories\\Contracts\\{$module}RepositoryInterface;\n\nclass {$module}Repository implements {$module}RepositoryInterface\n{\n    public function all()\n    {\n        return {$module}::all();\n    }\n\n    public function find(\$id)\n    {\n        return {$module}::findOrFail(\$id);\n    }\n\n    public function create(array \$data)\n    {\n        return {$module}::create(\$data);\n    }\n\n    public function update(\$id, array \$data)\n    {\n        \$record = \$this->find(\$id);\n        \$record->update(\$data);\n        return \$record;\n    }\n\n    public function delete(\$id)\n    {\n        \$record = \$this->find(\$id);\n        return \$record->delete();\n    }\n}\n";
    file_put_contents("$repoDir/{$module}Repository.php", $classContent);
    
    // Service
    $serviceContent = "<?php\n\nnamespace App\\Services;\n\nuse App\\Repositories\\Contracts\\{$module}RepositoryInterface;\n\nclass {$module}Service\n{\n    protected \$repository;\n\n    public function __construct({$module}RepositoryInterface \$repository)\n    {\n        \$this->repository = \$repository;\n    }\n\n    public function all()\n    {\n        return \$this->repository->all();\n    }\n\n    public function find(\$id)\n    {\n        return \$this->repository->find(\$id);\n    }\n\n    public function create(array \$data)\n    {\n        return \$this->repository->create(\$data);\n    }\n\n    public function update(\$id, array \$data)\n    {\n        return \$this->repository->update(\$id, \$data);\n    }\n\n    public function delete(\$id)\n    {\n        return \$this->repository->delete(\$id);\n    }\n}\n";
    file_put_contents("$servicesDir/{$module}Service.php", $serviceContent);
}

// Additional Services
$additionalServices = ['Notification']; // AuditService is already there
foreach ($additionalServices as $module) {
    $serviceContent = "<?php\n\nnamespace App\\Services;\n\nclass {$module}Service\n{\n    // Add business logic methods here\n}\n";
    file_put_contents("$servicesDir/{$module}Service.php", $serviceContent);
}

echo "Generated successfully.\n";
