<template>
    <Layout>
        <Head><title>Eduket | {{department.name}}</title></Head>
        <page-header :links="[{name:'departments.index',title:'Departments'}]" :current-page="department.name+' Department'" :page="department.name"/>
        <div class="row mb-30">
            <div class="col-md-5">
                <div class="card-box pd-20 clearfix mb-30">
                    <h5 class="h5">Events <button  @click="task = false" data-toggle="modal" data-target="#exampleModal" type="button" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus"></i></button></h5>
                    <div  class="feeds-box mt-20">

                        <div class="browser-visits">
                            <ul>
                                <li v-for="event in activities" v-if="activities.length > 0" class="d-flex flex-wrap align-items-center">
                                    <div class="custom-control custom-checkbox mb-5">
                                        <input @click="addItem(event.id,'e')"  type="checkbox" class="custom-control-input" id="customCheck1">
                                        <label style="width: 100%!important;" class="custom-control-label" for="customCheck1">
                                            <div style="width: auto;"  class="browser-name mr-1">{{event.title}} <br>
                                                <span class="fa fa-calendar"></span> <small>{{event.end | myDate }} (Due {{event.end | daysToGo}})</small> <br>
                                                <div class="progress mb-20 mt-15" style="height: 5px;">
                                                    <div class="progress-bar" role="progressbar" :style="'width:'+ daysRemaining(event)+'%'" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                </li>
                            </ul>
                            <button @click="markDone" v-if="selectedEvents.length>0" class="btn btn-primary pull-right">Mark as Done</button>
                        </div>
                    </div>
                    <length message="No recent events have been created" :category="activities"/>
                </div>
                <div class="card-box pd-20  clearfix mb-30">
                    <h5 class="h5">Tasks <button  @click="task = true" data-toggle="modal" data-target="#exampleModal" type="button" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus"></i></button></h5>
                    <div v-for="event in tasks" v-if="tasks.length > 0" class="feeds-box mt-20">

                        <div class="browser-visits">
                            <ul>
                                <li class="d-flex flex-wrap align-items-center">
                                    <div class="custom-control custom-checkbox mb-5">
                                        <input @click="addItem(event.id,'t')"  type="checkbox" class="custom-control-input" id="customCheck">
                                        <label style="width: 100%!important;" class="custom-control-label" for="customCheck">
                                            <div style="width: 100%!important;"  class="browser-name mr-1">{{event.title}} <br>
                                                <span class="fa fa-calendar"></span> <small>{{event.end | myDate }} (Due {{event.end | daysToGo}})</small> <br>
                                                <div class="progress mb-20 mt-15" style="height: 5px;">
                                                    <div class="progress-bar" role="progressbar" :style="'width:'+ daysRemaining(event)+'%'" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                </li>
                            </ul>
                            <button @click="markDone" v-if="selectedTasks.length>0" class="btn btn-primary pull-right">Mark as Done</button>
                        </div>
                    </div>
                    <length message="No recent tasks have been created" :category="tasks"/>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card-box pd-20 pb-30 mb-30 clearfix">
                    <form @submit.prevent="sendMessage" id="post">
                        <div class="form-group">
                            <textarea v-model="message.body" rows="3" class="form-control" placeholder="Quick message to members"></textarea>
                        </div>
                        <small v-if="message.errors.message" class="text-danger">{{message.errors.message}}</small>

                        <button  type="button" value="Post" class="btn btn-primary pull-right"><i class="fa fa-paper-plane"></i> Send</button>
                    </form>
                </div>
                <div class="card-box pd-20 mb-30">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Load</th>
                            <th scope="col">Role</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(user,i) in members">
                            <th scope="row">{{i+1}}</th>
                            <td>{{user.lname+' '+user.fname}}</td>
                            <td>{{user.load}}</td>
                            <td>{{user.role?user.role.name:'Member'}}</td>
                            <td>
                                <button data-placement="top" data-toggle="tooltip" title="Remove Member" class="btn btn-sm btn-danger" @click.prevent="deleteMember(user.id)">
                                    <span class="fa fa-trash"></span>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <Modal id="exampleModal" :title="task?'Create Task':'Create Event'" :submit="createEvent">
            <template #body>
                    <div class="form-group">
                        <label >Name</label>
                        <input  v-model="events.title" class="form-control">
                        <small v-if="events.errors.title" class="text-danger">{{events.errors.title}}</small>
                    </div>
                    <div class="form-group">
                        <label >Start Date</label>
                        <datetime :minute-step="5" :format=" 'yyyy-MM-dd HH:mm' " input-class="form-control" type="datetime" v-model="events.start"></datetime>
                        <small v-if="events.errors.start" class="text-danger" >{{events.errors.start}}</small>
                    </div>

                    <div class="form-group">
                        <label >End Date</label>
                        <datetime :minute-step="5"  format="yyyy-MM-dd HH:mm" input-class="form-control" type="datetime" v-model="events.end"></datetime>
                        <small v-if="events.errors.end" class="text-danger" >{{events.errors.end}}</small>
                    </div>

                    <div v-if="task" class="form-group">
                        <label>Members Assigned</label>
                        <multiselect :searchable="true" :multiple="true" label="name" track-by="id" v-model="events.members" :options="users"></multiselect>
                    </div>
                    <div class="form-group">
                        <label >Description</label>
                        <textarea  v-model="events.description" class="form-control"></textarea>
                        <small v-if="events.errors.description" class="text-danger" >{{events.errors.description}}</small>
                    </div>
            </template>
            <template #footer>
                <button type="submit"  class="btn btn-primary">Add{{task?' Task':' Event'}}</button>
            </template>
        </Modal>

    </Layout>
</template>

<script>
export default {
    name: "Department",
    props: {
        activities:[],
        tasks:[],
        users:[],
        members:[],
        department:{},

    },

    data() {
        return {
            task:false,
            selectedTasks:[],
            selectedEvents:[],
            message: this.$inertia.form({
                body:'',
            }),
            events: this.$inertia.form({
                title:'',
                start:'',
                end:'',
                description:'',
                members:[],
                type:''
            }),

        }
    },

    methods: {
        sendMessage() {

        },
       markDone() {
           Swal.fire({
               title: 'Are you sure?',
               text: "Selected "+(this.task?'tasks':'events')+"  will be marked as completed and will be removed?",
               icon: 'warning',
               showCancelButton: true,
               confirmButtonColor: '#3085d6',
               cancelButtonColor: '#d33',
               confirmButtonText: 'Yes, marked as completed!'
           }).then((result) => {
               if (result.value) {

                   this.message.transform((data)=>({
                       ...data,
                       members:this.tasks?this.selectedTasks:this.selectedEvents,
                       type: this.task?'Task':'Event',
                       start:moment(this.events.start).format('Y-MM-DD HH:mm'),
                       end:moment(this.events.end).format('Y-MM-DD HH:mm')
                   })).delete(this.$route('department.destroy',this.department.id),{
                       preserveState:true,
                       preserveScroll:true,
                       onSuccess: ()=> {
                           Swal.fire(
                               'Removed!',
                               (this.task?'Tasks':'Events')+' were removed',
                               'success'
                           )

                       }
                   })

               }
           })
       },
        addItem(id,type) {

            if (type === 't') {
                if (!this.selectedTasks.includes(id)) {
                    this.selectedTasks.push(id)
                }
                else {
                    this.selectedTasks = this.selectedTasks.filter((item)=>item !== id)
                }
            }
            else {
                if (!this.selectedEvents.includes(id)) {
                    this.selectedEvents.push(id)
                }
                else {
                    this.selectedEvents = this.selectedEvents.filter((item)=>item !== id)
                }
            }

        },
        daysRemaining(event) {
           var days = moment(event.end).diff(moment(event.created_at),'days')
           var remaining = moment(event.end).diff(moment(new Date()),'days')

            if (remaining<=0) return 0
            return ((remaining/days)*100).toFixed(0)

        },
        createEvent() {

            this.events.transform((data)=>({
                ...data,
                members:this.events.members.map((user)=>user.id),
                type: this.task?'Task':'Event',
                start:moment(this.events.start).format('Y-MM-DD HH:mm'),
                end:moment(this.events.end).format('Y-MM-DD HH:mm')
            })).post(this.$route('department.store'),{
                preserveState:true,
                preserveScroll:true,
                onSuccess: ()=> {
                    $('#exampleModal').modal('hide')
                    Swal.fire(
                        'Created!',
                        (this.task?'Task':'Event')+' was created and notifications sent to any assigned members',
                        'success'
                    )
                }
            })
        },
        deleteMember(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "Member will be removed from the department?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete user!'
            }).then((result) => {
                if (result.value) {

                    this.message.delete(this.$route('departments.destroy',[this.department.id,{u:id}]),{
                        preserveState:true,
                        preserveScroll:true,
                        onSuccess: ()=> {
                            Swal.fire(
                                'Removed!',
                                'User was removed',
                                'success'
                            )

                        }
                    })

                }
            })
        },
    },
}
</script>

<style scoped>

</style>
