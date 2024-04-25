<template>
    <Layout>
        <Head><title>Eduket | Exam Timetable</title></Head>
        <page-header current-page="Exam Timetable" page="Examinations"/>
        <div class="pd-20 card-box mb-30">
            <h4 class="text-center">End of Term {{term.number}} Timetable
                <a @click.prevent="publishTime" v-if="status > 0" href="" class="btn btn-primary btn-sm pull-right text-white"> <i class="fa fa-clipboard-check"></i> Publish</a>
                <a v-if="publish > 0 && status === 0"  href="/export/examtimetable" class="btn btn-primary btn-sm pull-right text-white  m-r-5"> <i class="fa fa-cloud-download-alt"></i> Download</a>
            </h4>

            <br>
            <div id="calendar"></div>
            <Modal id="create" :title="(editMode?'Edit':'Create')+' Exam Paper'" :submit="editMode?updateEvent:createEvent" >
                <template #body>
                        <alert message="Either no teacher on duty or supervisor or subject was selected" :alert="alert" />
                        <div class="form-group">
                            <label >Start Time</label>
                            <datetime :minute-step="5" input-class="form-control" type="time" v-model="form.start"></datetime>
                            <span class="text-danger" v-if="form.errors.start">{{form.errors.start}}</span>
                        </div>
                        <div class="form-group">
                            <label >End Time</label>
                            <datetime :minute-step="5"  title="Select Date" input-class="form-control" type="time" v-model="form.end"></datetime>
                            <span class="text-danger" v-if="form.errors.end">{{form.errors.end}}</span>
                        </div>

                        <div class="form-group">
                            <label>Supervisor</label>
                            <multiselect multiple  v-model="form.supervisors" :options="selectedMembers" label="name" track-by="id"></multiselect>
                        </div>
                        <div class="form-group">
                            <label>TOD</label>
                            <multiselect  v-model="form.tod"  :options="nonMembers" label="name" track-by="id" multiple></multiselect>
                        </div>
                        <div class="form-group">
                            <label>Class</label>
                            <select class="form-control" v-model="form.form" id="">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Subject</label>
                            <multiselect  v-model="form.subject" :options="subjects" label="name" track-by="id"></multiselect>
                        </div>
                        <div v-if="form.form>2" class="form-group">
                            <label>Paper Category</label>
                            <select class="form-control" v-model="form.paper">
                                <option>I</option>
                                <option>II</option>
                                <option>III</option>
                                <option>IV</option>
                            </select>
                        </div>
                </template>
                <template #footer>
                    <button type="submit" class="btn btn-primary" >{{editMode?'Update':'Create'}} Event</button>
                </template>
            </Modal>

            <Modal id="exampleModal" title="Exam Event Details" >
                <template #body>
                    <h5 >Subject</h5>
                    <p>{{event.title}}</p>
                    <h5 class="card-title">Class</h5>
                    <p>Form {{event.form}}</p>
                    <h5 class="card-title">Time</h5>
                    <p v-if="event.start">{{event.start.format('Do MMMM Y')}} <span class="text-bold">({{timeCalc(event.start,event.end)}})</span></p>
                    <h5 class="card-title">Supervisor</h5>
                    <p v-for="user in event.user">{{user.fname+' '+user.lname}}</p>

                    <h5 class="card-title">Teachers on Duty</h5>
                    <p v-for="tod in event.users">{{tod.fname+' '+tod.lname+' '}}</p>

                </template>
                <template #footer>
                    <button type="button" @click.prevent="deleteEvent" class="btn btn-danger" >Remove Event</button>
                    <button type="button" @click.prevent="editEvent(event)" class="btn btn-success" >Edit Event</button>
                </template>
            </Modal>

        </div>
    </Layout>
</template>

<script>
export default {
    name: "Examinations",
    props: {
        term:{},
        users:[],
        subjects:[],
        status:0,
        publish:0
    },

    data() {
        return {
            form:this.$inertia.form({
                id:'',
                subject:'',
                form:'',
                tod:[],
                supervisors:[],
                day:'',
                paper:'',
                start:'',
                end:'',
            }),
            alert:'',
            editMode:false,
            event:{},
            hour:'',
            minute:'',

        }
    },
    computed:{
        nonMembers(){
            return  this.users.filter(member=>{
                return !this.form.supervisors.some(hod=>{
                    return hod.id === member.id
                })
            })
        },
        selectedMembers(){
            return  this.users.filter(member=>{
                return !this.form.tod.some(hod=>{
                    return hod.id === member.id
                })
            })
        },

    },
    mounted() {
        var vm = this
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,basicWeek,basicDay'
            },
            validRange:{
                start: vm.term.opening,
                end: vm.term.closing,
            },
            editable:true,
            themeSystem: 'bootstrap4',
            defaultDate: new Date(),
            events(start, end, timezone, callback) {
                var starts = moment(start).format('YYYY-MM-DD')
                var ends = moment(end).format('YYYY-MM-DD')
                axios.get( vm.$route('examinations.index',{s:starts,e:ends,t:1}), {timezone: timezone}).then(response => {
                    callback(response.data)
                })
            },
            eventClick(event) {
                $('#exampleModal').modal('show')
                vm.event = event
            },
            dayClick(event) {
                vm.form.day = event.format('Y-MM-DD')
                $('#create').modal('show')
            },
            eventDrop(event) {
                axios.post(vm.$route('examinations.update',event.id),{s:event.start.format('Y-M-D H:mm'),e:event.end.format('Y-M-D H:mm'),'_method': 'put','u':true})
            }
        })

            },

    methods: {
        timeCalc(start,end) {
            let hours = moment.duration(moment(end).diff(moment(start))).asHours()
            let minutes = moment.duration(moment(end).diff(moment(start))).asMinutes()
            let diff = minutes-hours*60
            return hours +' hours '+(diff>0?diff+' minutes':'');
        },
        updateEvent() {

            if (Object.keys(this.form.subject).length>0 && this.form.tod.length >0 && this.form.supervisors.length>0) {

                this.form.transform((data)=>({
                    ...data,
                    subject:this.form.subject.name,
                    tod: this.form.tod.map((tod)=>tod.id),
                    supervisors: this.form.supervisors.map((tod)=>tod.id),
                })).put(this.$route('examinations.update',this.form.id), {
                    preserveState:true,
                    onSuccess:()=>{
                        $('#calendar').fullCalendar('refetchEvents')
                        this.form.reset()
                        $('#create').modal('hide')
                        Swal.fire(
                            'Event Update!',
                            'Event was successfully updated',
                            'success'
                        )
                    }
                })

            }
            else {
                this.alert = ''
                this.alert = 'danger'
            }

        },
        editEvent (event) {
            this.editMode = true
            this.form.supervisors =  event.user.map(member=> {
                return {'id':member.id,'name':member.fname + ' '+ member.lname}
            })

            this.form.tod =  event.users.map(member=> {
                return {'id':member.id,'name':member.fname + ' '+ member.lname}
            })

            this.form.id  = event.id
            this.form.form  = event.form
            this.form.subject  = this.subjects.filter((sub)=>sub.name === event.title)[0]
            this.form.start = event.start.format('HH:mm')
            this.form.end = event.end.format('HH:mm')
            this.form.paper  = event.paper
            this.form.day  =  event.start.format('Y-MM-DD')

            $('#exampleModal').modal('hide')
            $('#exampleModal').on('hidden.bs.modal',function () {
                $('#create').modal('show')
            })



        },
        publishTime(){
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, publish timetable!'
            }).then((result) => {
                if (result.value) {
                    this.$inertia.get(this.$route('timetable.show','publish'), {},{
                        preserveState:true,
                        onSuccess: ()=> {
                            this.status = 10
                            Swal.fire(
                                'Published!',
                                'Timetable has been published',
                                'success'
                            )
                        }
                    })
                }
            })

        },

        deleteEvent() {
            this.form.delete(this.$route('timetable.destroy',this.event.id)).then(()=> {

                $('#calendar').fullCalendar('refetchEvents')
                $('#exampleModal').modal('hide')
                Swal.fire(
                    'Removed!',
                    'Examination event was removed successfully',
                    'success'
                )
            })
        },
        createEvent(){
            if (Object.keys(this.form.subject).length>0 && this.form.tod.length >0 && this.form.supervisors.length>0) {

                this.form.transform((data)=>({
                    ...data,
                    subject:this.form.subject.name,
                    tod: this.form.tod.map((tod)=>tod.id),
                    supervisors: this.form.supervisors.map((tod)=>tod.id),
                    start:moment(this.form.start).format('HH:mm'),
                    end:moment(this.form.end).format('HH:mm')
                })).post(this.$route('examinations.store'), {
                    preserveState:true,
                    onSuccess:()=>{
                        $('#calendar').fullCalendar('refetchEvents')
                        this.form.reset()
                        $('#create').modal('hide')
                        Swal.fire(
                            'Event Created!',
                            'Event was successfully created',
                            'success'
                        )
                    }
                })

            }
            else {
                this.alert = ''
                this.alert = 'danger'
            }



        },

    },

}
</script>

<style scoped>

</style>
