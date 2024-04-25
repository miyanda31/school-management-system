<template>
    <Layout>
        <Head><title>Eduket | Events</title></Head>
        <page-header current-page="School Events" page="Events"/>
        <div class="pd-20 card-box mb-30">
            <h4 class="text-center">Calendar of Events | Term {{term.number}}
                <label class="pull-right">
                    <select @change="eventType" class="form-control" v-model="type" >
                        <option value="">All Events</option>
                        <option>Club</option>
                        <option>School</option>
                        <option>Administrative</option>
                        <option>Department</option>
                        <option>Public</option>
                    </select>
                </label>

                <a @click.prevent="publishEvents" v-if="status > 0" href="" class="btn btn-primary btn-sm pull-right text-white"> <i class="fa fa-clipboard-check"></i> Publish</a>
            </h4>

            <br>
            <div id="calendar"></div>
            <Modal id="create" :title="(editMode?'Edit':'Create')+' Event'" :submit="editMode?updateEvent:createEvent" >
                <template #body>
                    <div class="form-group">
                        <label>Event Type</label>
                        <select class="form-control" v-model="form.type" id="">
                            <option>Club</option>
                            <option>School</option>
                            <option>Administrative</option>
                            <option>Department</option>
                            <option>Public</option>
                        </select>
                        <small v-if="form.errors.type" class="text-danger" >{{form.errors.type}}</small>
                    </div>
                    <div class="form-group">
                        <label >Title</label>
                        <input  v-model="form.title" class="form-control">
                        <small v-if="form.errors.description" class="text-danger" >{{form.errors.description}}</small>
                    </div>
                    <div class="form-group">
                        <label >Start Time</label>
                        <datetime format="HH:mm" :minute-step="5" input-class="form-control" type="time" v-model="form.start"></datetime>
                        <span class="text-danger" v-if="form.errors.start">{{form.errors.start}}</span>
                    </div>
                    <div class="form-group">
                        <label >End Time</label>
                        <datetime format="DD HH:mm" :minute-step="5"  title="Select Date" input-class="form-control" type="datetime" v-model="form.end"></datetime>
                        <span class="text-danger" v-if="form.errors.end">{{form.errors.end}}</span>
                    </div>
                    <div class="form-group">
                        <label >Description</label>
                        <textarea  v-model="form.description" class="form-control"></textarea>
                        <small v-if="form.errors.description" class="text-danger" >{{form.errors.description}}</small>
                    </div>
                </template>
                <template #footer>
                    <button type="submit" class="btn btn-primary" >{{editMode?'Update':'Create'}} Event</button>
                </template>
            </Modal>

            <Modal id="exampleModal" title="Event Details" >
                <template #body>
                    <h5 >Event Type</h5>
                    <p>{{event.type}}</p>
                    <h5 >Subject</h5>
                    <p>{{event.title}}</p>
                    <h5 class="card-title">Event Date & Time</h5>
                    <p v-if="event.start">{{event.start.format('Do MMM Y HH:mm')}} - {{event.end.format('Do MMM Y HH:mm')}}</p>
                    <h5 class="card-title">Event Summary</h5>
                    <p>{{event.description}}</p>

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
    name: "Events",
    props: {
        status:0,
        term:{}
    },
    data() {
        return {
            alert:'',
            editMode:false,
            event:{},
            hour:'',
            minute:'',
            type:'',
            form:this.$inertia.form({
                id:'',
                title:'',
                type:'',
                description:'',
                start:'',
                end:'',
                day:''
            }),
        }
    },
    mounted() {
        var vm = this
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,basicWeek,basicDay'
            },
            editable:true,
            themeSystem: 'bootstrap4',
            defaultDate: new Date(),
            events(start, end, timezone, callback) {
                var starts = moment(start).format('YYYY-MM-DD')
                var ends = moment(end).format('YYYY-MM-DD')

                axios.get( vm.$route('events.index',{s:starts,e:ends,t:vm.type})).then(response => {
                    callback(response.data)
                })
            },
            eventClick(event) {
                vm.event = event
                $('#exampleModal').modal('show')
            },
            dayClick(event) {
                vm.form.day = event.format('Y-MM-DD')
                $('#create').modal('show')
            },
            eventDrop(event) {
                axios.post(vm.$route('events.update',event.id),{s:event.start.format('Y-M-D H:mm'),e:event.end.format('Y-M-D H:mm'),'_method': 'put','u':true})
            }
        })

    },

    methods: {
        eventType() {
            $('#calendar').fullCalendar('refetchEvents');
        },
        timeCalc(start,end) {
            let hours = moment.duration(moment(end).diff(moment(start))).asHours()
            let minutes = moment.duration(moment(end).diff(moment(start))).asMinutes()
            let diff = minutes-hours*60
            return hours +' hours '+(diff>0?diff+' minutes':'');
        },
        updateEvent() {
                this.form.transform((data)=>({
                    ...data,
                    start:moment(this.form.start).format('HH:mm'),
                    end:moment(this.form.end).format('Y-MM-DD HH:mm'),
                })).put(this.$route('events.update',this.form.id), {
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
        },
        editEvent (event) {
            this.editMode = true

            this.form.id  = event.id
            this.form.start = event.start.format('HH:mm')
            this.form.end = event.end.format('Y-MM-DD HH:mm')
            this.form.type = event.type
            this.form.description = event.description
            this.form.title = event.title
            this.form.day  =  event.start.format('Y-MM-DD')

            $('#exampleModal').modal('hide')
            $('#exampleModal').on('hidden.bs.modal',function () {
                $('#create').modal('show')
            })



        },
        publishEvents(){
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, publish events!'
            }).then((result) => {
                if (result.value) {
                    this.$inertia.get(this.$route('events.show','publish'), {},{
                        preserveState:true,
                        onSuccess: ()=> {
                            this.status = 10
                            Swal.fire(
                                'Published!',
                                'Event has been published',
                                'success'
                            )
                        }
                    })
                }
            })

        },

        deleteEvent() {
            this.form.delete(this.$route('events.destroy',this.event.id)).then(()=> {

                $('#calendar').fullCalendar('refetchEvents')
                $('#exampleModal').modal('hide')
                Swal.fire(
                    'Removed!',
                    'Event was removed successfully',
                    'success'
                )
            })
        },
        createEvent(){
                this.form.transform((data)=>({
                    ...data,
                    start:moment(this.form.start).format('HH:mm'),
                    end:moment(this.form.end).format('Y-MM-DD HH:mm'),
                })).post(this.$route('events.store'), {
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

        },

    },

}
</script>

<style scoped>

</style>
