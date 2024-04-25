<template>
    <Layout>
        <Head><title>Eduket | Timetable</title></Head>
        <page-header current-page="Master Timetable" page="Master"></page-header>
        <div class="card-box pd-20 height-100-p mb-30">
            <div v-if="status" class="pull-right">
                <a  @click.prevent="publishTimetable" href="" class="btn btn-sm btn-primary ">Publish Timetable</a>
            </div>
            <button v-if="status" class="btn btn-sm btn-primary pull-right m-r-5 visible-lg visible-md" @click="printGrades" ><i class="fa fa-print"></i></button>
            <h4 class="text-center card-title text-uppercase visible-lg visible-md">Shift {{shift.shift}} Timetable | Form {{shift.number}}</h4>
            <label style="display: inline-flex" class="pull-left">
                <select @change="loadCategory" v-model="type" class="form-control input-sm ml-2 mr-2">
                    <option disabled value="">Shift</option>
                    <option v-for="shift in shifts" >{{shift.shift}}</option>
                </select>
            </label>
            <label style="display: inline-flex" class="pull-left">
                <select @change="loadCategory" v-model="forms" class="form-control input-sm ml-2 mr-2">
                    <option disabled value=""> Class</option>
                    <option v-for="i in 4" > {{i}}</option>
                </select>
            </label>
            <br>
            <div class="col-md-12" v-if="times.length>0">
                <table id="timetable" class="table table-bordered table-condensed ">
                    <thead >
                    <tr>
                        <th class="border-bottom-0 text-center" colspan="2">
                            <draggable :options="options"   @add="removeSubject($event)"   :element="'span'" class="fa fa-trash fa-3x deleteItem" ></draggable>
                        </th>
                        <th class="text-center" v-if="time.type === 1" v-for="time in times">{{time.period}}</th>
                        <th v-else-if="time.type === 0" class="border-0" >B</th>
                    </tr>
                    <tr>
                        <th colspan="2" :class="count>0?'border-bottom-0':''"></th>
                        <th class="text-center" v-if="time.type === 1" v-for="time in times">{{time.time}}</th>
                        <th v-else-if="time.type === 0" class="border-0"></th>
                    </tr>
                    <tr v-if="count>0">
                        <th></th>
                        <th></th>
                        <th class="text-center" v-if="time.type === 1" v-for="time in times">{{time.alttime}}</th>
                        <th v-else-if="time.type === 0" class="border-0" ></th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr v-for="(day,index) in days">
                        <td class="align-middle">{{short[index].toUpperCase()}}</td>
                        <td>
                            <table class="table-sm table table-borderless">
                                <thead>
                                <tr v-for="c in classes"><th class="text-center">{{c.name}}</th></tr>
                                </thead>
                            </table>
                        </td>
                        <td  @click="addAllocation(time.id,day)" v-for="time in times">
                            <table class="table-sm table table-borderless">
                                <thead>
                                <tr v-for="c in classes">
                                        <draggable class="text-center" @add="movePeriod($event, day,time.id)"   :list="timetable" :options="options"  :element="'th'" >
                                            <div :data-id="period.id" :key="pos" v-for="(period,pos) in timetable" v-if="period.time_id === time.id && period.dayname === day && c.id === period.allocation.form_id ">
                                          <span style="cursor: move">{{period.allocation.subject.short_code}} ({{period.allocation.code}})
                                          </span>
                                            </div>
                                        </draggable>
                                </tr>
                                </thead>
                            </table>


                        </td>
                    </tr>

                    </tbody>

                </table>
            </div>
            <length :category="times" message="Time table structure has not been set up please wait until the timetable is set up"></length>

            <div id="add" class="modal fade" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header ">
                            <h4 class="modal-title">Create Allocation</h4>
                            <button type="button" class="close" data-dismiss="modal">×</button>
                        </div>
                        <form @submit.prevent="createAllocation" >
                            <div class="modal-body">
                                <div v-if="form.errors.crashes" class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{form.errors.crashes}}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="form-group" >
                                    <label >Name</label>
                                    <multiselect  @searchable="true" @search-change="searchUser" placeholder="Search User" track-by="id" label="name" v-model="user" :options="users"></multiselect>

                                </div>
                                <div v-if="Object.keys(user).length > 0" class="col-md-12 col-sm-12">
                                    <label class="weight-600">Select Allocation</label>
                                    <div v-for="allocate in user.allocation" class="custom-control custom-radio mb-5">
                                        <input :value="allocate.id" type="radio" :id="'customRadio'+allocate.id" v-model="form.id" class="custom-control-input">
                                        <label class="custom-control-label" :for="'customRadio'+allocate.id">{{allocate.form.number}}{{allocate.form.name}} {{allocate.subject.name}} </label>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Allocate</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
import  Draggable from 'vuedraggable'

export default {
    name: "Main",
    components: {
        Draggable,
    },
    props: {
        times:[],
        timetable:[],
        subjects:[],
        classes:[],
        periods:[],
        shifts:{},
        shift:{},
    },
    data() {
        return {
            user:{},
            users:[],
            days:['Monday','Tuesday','Wednesday','Thursday','Friday'],
            short:['Mon','Tue','Wed','Thu','Fri'],
            period:'',
            allocation:'',
            count:0,
            options:{
                animation:200,
                group:'Periods',

            },

            status:'',
            type:'',
            forms:'',
            index:new Date().getDay()-1,
            form:this.$inertia.form({
                id:"",
                time:"",
                day:"",
                user_id:"",
            }),


        }
    },
    methods: {
        loadCategory() {
            this.$inertia.get(this.$route('timetable.index'),{'s':this.type,'c':this.forms},{preserveState:true})
        },

        searchUser(user){
            if (user) {
                axios.get(this.$route('timetable.show',[user,{s:true}])).then(({data})=>{
                    this.users = data;
                })
            }
        },
        addAllocation(id,day) {
            this.form.reset()
            this.user = {}
            this.form.time  = id
            this.form.day  = day
            $('#add').modal('show')
        },
        createAllocation() {
            this.form.transform((data)=>({
                ...data,
                user_id:this.user.id
            })).post(this.$route('timetable.store'),{
                preserveState:true,
                onSuccess: ()=> {
                    $('#add').modal('hide')
                    swal(
                        'User allocated!',
                        'User was successfully allocated',
                        'success'
                    )
                },
            })

        },

        movePeriod(event,day,time) {
            let obj = {}

            var id =event.item.getAttribute('data-id')
            this.timetable.forEach( (subject) =>{
                if (subject.id === parseInt(id)) {
                    obj = subject
                }

            })


           let form = this.form.transform((data)=>({
                ...data,
                user_id:this.user.id,
                id: obj.allocation_id,
                day: day,
                time: time
            }))

            Swal.fire({
                title: 'Specify action?',
                text: "Would you like to move period to selected or copy?",
                type: 'warning',
                icon:'question',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                denyButtonColor: '#600b84',
                confirmButtonText: 'Move Period',
                denyButtonText: 'Copy Period',
            }).then((result) => {
                if (result.isConfirmed) {
                 form.put(this.$route('timetable.update',obj.id),{
                     preserveState:true,
                        onSuccess:()=> {
                            swal(
                                'Deleted!',
                                'Timetable was successfully updated',
                                'success'
                            )
                        }
                    })

                }
                else if (result.isDenied) {
                    form.post(this.$route('timetable.store'),{
                        preserveState:true,
                        onSuccess:()=> {
                            swal(
                                'Deleted!',
                                'Timetable was successfully updated',
                                'success'
                            )
                        }
                    })
                }
            })

        },

        removeSubject(event){

            Swal.fire({
                title: 'Remove from Timetable?',
                text: "This can not be undone. Do you really want to remove period from timetable?",
                type: 'warning',
                icon:'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Remove Period',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.form.delete(this.$route('timetable.destroy',event.item.getAttribute('data-id')),{
                        preserveState:true,
                        onSuccess:()=> {
                            swal(
                                'Removed!',
                                'Timetable was successfully updated',
                                'success'
                            )
                        }
                    })

                }
                else {
                    this.$inertia.reload({only:['timetable']})
                }
            })

        },

        publishTimetable(){
            swal({
                title: 'Are you sure?',
                text: "Timetable will become available to public!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, publish!'
            }).then((result) => {
                if (result.value) {
                    var loader = $('#load')
                    loader.show()
                    axios.put(prefix+'admin/time/'+this.$route.params.shift+'?c='+this.$route.params.number+'&p=true').then(()=>{
                        this.alert = 'success'
                        swal(
                            'Published!',
                            'Timetable was successfully published. A notification was sent to affected parties ',
                            'success'
                        )
                        loader.hide()
                    })

                }
            })
        },


    },

}
</script>

<style scoped>

</style>
