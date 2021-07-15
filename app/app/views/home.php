<!doctype html>
<html lang="ru">
<head>
    <!-- Обязательные метатеги -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios@0.12.0/dist/axios.min.js"></script>

    <!--  element-ui Componets -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
    <!--    <script src="//unpkg.com/element-ui/lib/umd/locale/en.js"></script>-->
    <script src="//unpkg.com/element-ui/lib/umd/locale/en.js"></script>
    <title>TODO</title>
</head>
<body>
<div id="app" class="container">
    <div class="row">
        <div class="col-sm-3">
            <button v-on:click="newProject" class="btn btn-success">Новый проект</button>
            <ul class="list-group">
                <project-item
                    v-for="item in projects"
                    v-bind:project="item"
                    v-bind:key="item.id"
                    v-on:change-project="changeProject"
                    v-on:delete-project="deleteProject"
                    v-on:update-project="updateProject"
                ></project-item>
            </ul>
        </div>
        <div class="col-sm-9">
            <tasks v-bind:project="project"
                   v-on:new-task="newTask"
            ></tasks>
            <task-item
                v-for="item in tasks"
                v-bind:task="item"
                v-bind:key="item.id"
                v-on:delete-task="deleteTask"
                v-on:update-task="updateTask"
            ></task-item>
            <task-modal
                v-bind:show="showTaskDialog"
                v-bind:task="activeTask"
                v-on:dialog-task-close="showTaskDialog=false"
                v-on:save-task="saveTask"
            ></task-modal>
            <project-modal
                v-bind:show="showDialogProject"
                v-bind:project="project"
                v-on:dialog-project-close="showDialogProject=false"
                v-on:save-project="saveProject"
            ></project-modal>
        </div>
    </div>
</div>
<script>
    ELEMENT.locale(ELEMENT.lang.en)

    let ProjectRepository = function () {
        this.getProjects = function () {
            return axios.get('/project/index')
        }
        this.createProject = function (project) {
            return axios.post('project/create', {project: project});
        }
        this.deleteProject = function (projectId) {
            return axios.delete('project/delete?project=' + projectId);
        }
        this.updateProject = function (project) {
            return axios.put('project/update', {project: project});
        }
    }

    let TaskRepository = function () {
        this.getTasks = function (projectId) {
            return axios.get('task/index?project=' + projectId);
        }
        this.createTask = function (projectId, task) {
            return axios.post('task/create?project=' + projectId, {task: task});
        }
        this.deleteTask = function (taskId) {
            return axios.delete('task/delete?task=' + taskId);
        }
        this.getTask = function (taskId) {
            return axios.get('task/get?task=' + taskId)
        }

        this.updateTask = function (task) {
            return axios.put('task/update', {task: task});
        }
    }

    Vue.component('project-item', {
        props: ['project'],
        template: '<li class="list-group-item" style="cursor: pointer" v-on:click="$emit(\'change-project\',project)">{{ project.title }}<a href="#" v-on:click="$emit(\'update-project\',project)">Update</a> | <a href="#" v-on:click="$emit(\'delete-project\',project.id)">Delete</a></li>'
    });

    Vue.component('task-item', {
        props: ['task'],
        template: '<li class="list-group-item" style="cursor: pointer"><s v-if=task.done>{{ task.title  }}</s> <span v-else=task.done>{{ task.title  }}</span>  <a href="#" v-on:click="$emit(\'update-task\',task)">Update</a> | <a href="#" v-on:click="$emit(\'delete-task\',task.id)">Delete</a></li>'
    });

    Vue.component('tasks', {
        props: ['project'],
        data: function () {
            return {
                showModalNewTask: false
            };
        },
        template:
            '<div>' +
            '<h2>Задачи проекта: {{ project.title }}</h2>' +
            '<button type="button" class="btn btn-success" v-on:click="$emit(\'new-task\')">Новая задача</button>' +
            '</div>',
        methods: {
            newTask: function () {
                this.showModalNewTask = true;
            }
        }
    });

    Vue.component('task-modal', {
        props: ['show', 'task'],
        template:
            '<div class="modal fade show" v-show="show"' +
            ' data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLiveLabel"' +
            ' style="display: block; padding-right: 15px;" aria-modal="true">' +
            '            <div class="modal-dialog" role="document">' +
            '        <div class="modal-content">' +
            '        <div class="modal-header">' +
            '        <h5 class="modal-title">{{ task.id ===null?\'Новая задача\':\'Редактирование задачи\' }} </h5>' +
            '    <button type="button" class="close" data-dismiss="modal" aria-label="Close" v-on:click = "$emit(\'dialog-task-close\')">' +
            '        <span aria-hidden="true">×</span>' +
            '    </button>' +
            '    </div>' +
            '    <div class="modal-body">' +
            '   <label for="task_title" class="form-label">Заголовок</label>  <input id="task_title" type="text" class="form-control" v-model="task.title">' +
            '   <div class="form-check">' +
            '     <input type="checkbox" class="form-check-input" id="task_done" v-model="task.done">' +
            '       <label class="form-check-label" for="task_done">Готово</label>' +
            '    </div>' +
            '   <label for="task_descr" class="form-label">Описание</label> ' +
            '   <textarea class="form-control" v-model="task.description" id="task_descr" cols="30" rows="10"></textarea>' +
            '   <label for="task_start" class="form-label">Начало</label> ' +
            '   <div class="row">  ' +
            '    <el-date-picker ' +
            '        v-model="task.start" ' +
            '        type="datetime"' +
            '        format="dd.MM.yyyy HH:mm"' +
            '        placeholder="Дата и время начала выполнения">' +
            '        </el-date-picker>' +
            '        </div>' +
            '   <label for="task_duration" class="form-label">Планируемое время</label> ' +
            '   <input id="task_duration" type="text" class="form-control" v-model="task.duration">' +
            '</div> ' +
            '    <div class="modal-footer">' +
            '    <button type="button" class="btn btn-secondary" data-dismiss="modal" v-on:click="$emit(\'dialog-task-close\')">Закрыть</button>' +
            '    <button type="button" class="btn btn-primary" v-on:click="$emit(\'save-task\',task)">Сохранить</button>' +
            '    </div> ' +
            '    </div> ' +
            '    </div>' +
            '    </div>'
    });

    Vue.component('project-modal', {
        props: ['show', 'project'],
        data: function () {
            return {
                'task': {
                    'title': ''
                }
            }
        },
        template:
            '<div  class="modal fade show" v-show="show"' +
            ' data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLiveLabel"' +
            ' style="display: block; padding-right: 15px;" aria-modal="true">' +
            '            <div class="modal-dialog" role="document">' +
            '        <div class="modal-content">' +
            '        <div class="modal-header">' +
            '        <h5 class="modal-title">{{ project.id ===null?\'Новый проект\':\'Редактирование проекта\' }} </h5>' +
            '    <button type="button" class="close" data-dismiss="modal" aria-label="Close" v-on:click = "$emit(\'dialog-project-close\')">' +
            '        <span aria-hidden="true">×</span>' +
            '    </button>' +
            '    </div>' +
            '    <div class="modal-body">' +
            '   <label for="project_title" class="form-label">Заголовок</label>  <input id="project_title" type="text" class="form-control" v-model="project.title">' +
            '   <label for="project_descr" class="form-label">Описание</label> ' +
            '   <textarea class="form-control" v-model="project.description" id="project_descr" cols="30" rows="10"></textarea>' +
            '   <label for="project_start" class="form-label">Начало</label> ' +
            '   <div class="row">  ' +
            '    <el-date-picker ' +
            '        v-model="project.start" ' +
            '        type="date"' +
            '        format="dd.MM.yyyy"' +
            '        placeholder="Дата выполнения">' +
            '        </el-date-picker>' +
            '        </div>' +
            '   <label for="project_duration" class="form-label">Планируемое время</label> ' +
            '   <input id="project_duration" type="text" class="form-control" v-model="project.duration">' +
            '</div> ' +
            '    <div class="modal-footer">' +
            '    <button type="button" class="btn btn-secondary" data-dismiss="modal" v-on:click="$emit(\'dialog-project-close\')">Закрыть</button>' +
            '    <button type="button" class="btn btn-primary" v-on:click="$emit(\'save-project\',project)">Сохранить</button>' +
            '    </div> ' +
            '    </div> ' +
            '    </div>' +
            '    </div>'
    });

    let defaultTask = {
        id: null,
        title: 'Новая задача',
        description: '',
        start: '',
        duration: '',
        done: false
    };

    let defaultProject = {
        'id': null,
        'title': 'Новый проект',
        'description': 'Супер новый проект',
        'start': '',
        'duration': ''
    };

    let app = new Vue({
        el: '#app',
        created: function () {
            let vm = this;

            let repository = new ProjectRepository();

            repository.getProjects().then(
                function (response) {
                    vm.projects = response.data
                    if (vm.projects[0]) {
                        vm.project = vm.projects[0];
                    }

                    if (vm.project) {
                        let rep = new TaskRepository();

                        rep.getTasks(vm.project.id).then(function (response) {
                            vm.tasks = response.data;
                        }).catch(function (error) {
                            console.error(error);
                        });
                    }
                }
            ).catch(function (error) {
                console.error(error);
            });
        },
        methods: {
            //APP
            changeProject: function (project) {
                let vm = this;

                vm.project = project

                let repository = new TaskRepository();
                repository.getTasks(project.id).then(function (response) {
                    vm.tasks = response.data;
                }).catch(function (error) {
                    console.error(error);
                });
            },
            // Tasks
            newTask: function () {
                this.activeTask = defaultTask;

                this.showTaskDialog = true;
            },
            updateTask: function (task) {
                this.activeTask = task;
                this.showTaskDialog = true;
            },
            saveTask: function (task) {
                let repository = new TaskRepository();
                let vm = this;

                if (task.id === null) {
                    repository.createTask(vm.project.id, task).then(function (response) {
                        vm.tasks.push(response.data)
                        vm.showTaskDialog = false;
                    });
                } else {
                    repository.updateTask(task).then(function (response) {
                        vm.activeTask = response.data
                        vm.showTaskDialog = false;
                    });
                }

            },
            //Projects
            newProject: function () {
                this.project = defaultProject;
                this.showDialogProject = true;
            },
            deleteTask: function (taskId) {
                let vm = this;
                let repository = new TaskRepository();
                repository.deleteTask(taskId);
                repository.getTasks(vm.project.id).then(function (response) {
                    vm.tasks = response.data;
                });
            },
            updateProject: function (project) {
                this.project = project;
                this.showDialogProject = true;
            },
            saveProject: function (project) {
                let r = new ProjectRepository();
                let vm = this;

                if (project.id === null) {
                    r.createProject(project).then(function (response) {
                        vm.projects.push(response.data);
                        vm.project = response.data;
                        vm.showDialogProject = false;
                    });
                } else {
                    //TODO: обновление
                    r.updateProject(project).then(function (response) {
                        vm.project = response.data;
                        vm.showDialogProject = false;
                    });
                }
            },
            deleteProject: function (project) {
                let r = new ProjectRepository();
                let vm = this;

                r.deleteProject(project).then(function (response) {
                    vm.projects.push(response.data);
                    r.getProjects(vm.project.id).then(function (response) {
                        vm.projects = response.data;
                    });
                    vm.showDialogProject = false;
                });
            },
        },
        data: {
            projects: [],
            tasks: [],
            project: defaultProject,
            showTaskDialog: false,
            showDialogProject: false,
            activeTask: defaultTask
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4"
        crossorigin="anonymous"></script>

</body>
</html>
