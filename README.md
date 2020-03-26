# Use php generator ansible playbook file. 

# Installation
### The recommended approach is to install the project through [Composer](https://getcomposer.org/).
```php
composer require laijim/php-playbook
```

#Instantiate
```php
//Instantiate
$pb = new \Laijim\Playbook\Playbook(
    new \Laijim\Playbook\Entity\Worker(
        __DIR__ . '/test',
        new \Laijim\Playbook\Entity\LocalHostsFileWriter(),
        new \Laijim\Playbook\Entity\LocalTasksFileWriter(),
        new \Laijim\Playbook\Entity\LocalVariablesFileWriter(),
        new Filesystem()
    )
);

//set host and variables
$pb->setHosts([
    'web' => ['node1', '127.0.0.1'],
    'db' => ['node1', '127.0.0.1'],
])->setVariables([
    'step1' => [
        "var1" => "var1",
        "var2" => "var2"
    ],
    'step2' => [
        "var1" => "var1",
        "var2" => "var2"
    ]
]);

//set tasks
$task1 = new \Laijim\Playbook\Task();
$task1->useHost('web')
    ->useVariables(['step1'])
    ->addTasks([
        ["name" => "debug 1", "debug" => "var=var1"],
        ["name" => "debug 2", "debug" => "var=var2"],
        ["name" => "step 3", "shell" => "echo handlers", "notify" => "step 4"],
    ])->directive('handlers', [
        ["name" => "step 4", "shell" => "echo final"],
    ]);

$task2 = new \Laijim\Playbook\Task();
$task2->useHost('db')
    ->useVariables(['step2'])
    ->addTasks([
        ["name" => "debug 1", "debug" => "var=var1"],
        ["name" => "debug 2", "debug" => "var=var2"],
        ["name" => "step 3", "shell" => "echo handlers", "notify" => "step 4"],
    ])->directive('handlers', [
        ["name" => "step 4", "shell" => "echo final"],
    ]);

//generate playbook files
$pb->register($task1)
->register($task2)
->generate();

```

It will finally generate this directory structure:

```
├── hosts
├── playbook.yaml
└── vars
    ├── step1.yaml
    └── step2.yaml
```

hosts:

```
[web]
node1
127.0.0.1

[db]
node1
127.0.0.1
```

playbook.yaml:
```
        -
        hosts: web
        vars_files:
            - ./vars/step1.yaml
        task:
            -
                name: 'debug 1'
                debug: var=var1
            -
                name: 'debug 2'
                debug: var=var2
            -
                name: 'step 3'
                shell: 'echo handlers'
                notify: 'step 4'
        handlers:
            -
                name: 'step 4'
                shell: 'echo final'
        -
        hosts: db
        vars_files:
            - ./vars/step2.yaml
        task:
            -
                name: 'debug 1'
                debug: var=var1
            -
                name: 'debug 2'
                debug: var=var2
            -
                name: 'step 3'
                shell: 'echo handlers'
                notify: 'step 4'
        handlers:
            -
                name: 'step 4'
                shell: 'echo final'

```

vars/stepN.yaml:
```html
var1: var1
var2: var2

```

vars/stepN.yaml:
```html
var1: var1
var2: var2

```
# Let's test it! 

```shell
ansible-playbook -i hosts playbook.yaml
```

# Operation result

```

PLAY [all] *****************************************************************************************************************************************

TASK [Gathering Facts] *****************************************************************************************************************************
ok: [127.0.0.1]
ok: [node2]

TASK [debug 1] *************************************************************************************************************************************
ok: [127.0.0.1] => {
    "var1": "var1"
}
ok: [node2] => {
    "var1": "var1"
}

TASK [debug 2] *************************************************************************************************************************************
ok: [127.0.0.1] => {
    "var2": "var2"
}
ok: [node2] => {
    "var2": "var2"
}

TASK [step 3] **************************************************************************************************************************************
changed: [127.0.0.1]
changed: [node2]

RUNNING HANDLER [debug 3] **************************************************************************************************************************
changed: [127.0.0.1]
changed: [node2]

PLAY RECAP *****************************************************************************************************************************************
127.0.0.1                  : ok=5    changed=2    unreachable=0    failed=0
node2                      : ok=5    changed=2    unreachable=0    failed=0
```