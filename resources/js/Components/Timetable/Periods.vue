<template>
    <Layout>
        <Head><title>Eduket | Periods</title></Head>
        <page-header current-page="Class Periods" page="Periods"/>
        <div class="card-box pd-20 height-100-p mb-30 col-md-8 ml-auto mr-auto">
            <h5 class="h5">Periods
                <a @click="addTime"  data-toggle="modal" data-target="#exampleModal" class="btn btn-primary btn-sm text-white pull-right"> <i class="fa fa-plus"></i></a>
            </h5>
            <br>

            <div v-if="periods.length>0" class="table-wrap">

                <table  class="table table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Start</th>
                        <th>End</th>
                        <th v-if="count>1">Alt Start</th>
                        <th v-if="count>1">Alt End</th>
                        <th>Type</th>
                        <th></th>
                    </tr>
                    </thead>
                    <draggable  :list="periods" :options="options"  @change="orderPeriods"   :element="'tbody'">
                        <tr :id="'time'+time.id" v-for="(time,i) in periods">
                            <td style="cursor: move;" class="move">{{i+1}}</td>
                            <td>{{time.start | times}}</td>
                            <td>{{time.end | times}}</td>
                            <td v-if="count>1"><span v-if="time.altstart">{{time.altstart | times}}</span></td>
                            <td v-if="count>1"><span v-if="time.altend">{{time.altend | times}}</span></td>
                            <td>{{time.type === 1?'Period':'Break'}}</td>
                            <td>
                                <button data-placement="top" data-toggle="tooltip" title="Edit Details"  @click="editTime(time)" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></button>
                                <button data-placement="top" data-toggle="tooltip" title="Remove Period" @click="removeTime(time.id)"   class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    </draggable>
                </table>
            </div>

            <length :category="periods" message="You have not set up periods for your time table yet. Please ensure this is done to produce the time table"></length>
        </div>

        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">

                        <h5 class="modal-title" id="exampleModalLabel">{{editMode?'Edit':'Add'}} Time Frame</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editMode?updateTime():createTime()" >
                    <div class="modal-body">

                            <div class="form-group">
                                <label >Start Time</label>
                                <datetime v-model="form.start" :minute-step="5" type="time" format="HH:mm" name="start"  input-class="form-control"></datetime>

                                <span class="text-danger" v-if="form.errors.start">{{form.errors.start}}</span>
                            </div>

                            <div class="form-group">
                                <label >End Time</label>
                                <datetime  v-model="form.end" :minute-step="5" type="time" format="HH:mm" name="start"  input-class="form-control"></datetime>

                                <span class="text-danger" v-if="form.errors.end">{{form.errors.end}}</span>
                            </div>

                            <div v-if="count > 1" class="form-group">
                                <label >Alternate Start Time</label>
                                <datetime  v-model="form.altstart" :minute-step="5" type="time" format="HH:mm" name="start"  input-class="form-control"></datetime>

                                <span class="text-danger" v-if="form.errors.altstart">{{form.errors.end}}</span>
                            </div>

                            <div v-if="count > 1" class="form-group">
                                <label >Alternate End Time</label>
                                <datetime v-model="form.altend" :minute-step="5" type="time" format="HH:mm" name="start"  input-class="form-control"></datetime>
                                <span class="text-danger" v-if="form.errors.altend">{{form.errors.end}}</span>
                            </div>

                            <div class="form-group">
                                <label >Type</label>
                                <select v-model="form.type" type="text" class="form-control"  >
                                    <option value="0">Break</option>
                                    <option value="1">Period</option>
                                </select>
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">{{editMode?'Update':'Create'}}</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </Layout>

</template>

<script>
import Draggable from 'vuedraggable'

export default {
    name: "Periods",
    props: {
        periods:[],
        total:0
    },
    components: {
        Draggable,
    },
    data() {
        return {
            count:0,
            editMode:false,
            options:{
                animation:200,
                group:'Periods',
                handle:".move"

            },
            form:this.$inertia.form({
                start:'',
                end:'',
                altstart:'',
                altend:'',
                type:'',
            }),
        }
    },
    methods: {
        orderPeriods(event) {
            this.form.transform((data)=>({
                ...data,
                start:event.moved.oldIndex,
                end:event.moved.newIndex,
                altend:this.periods[event.moved.oldIndex].id,
                t:true
            })).put(this.$route('periods.update',event.moved.element.id))
        },
        editTime(time) {

            this.form.id = time.id
            this.form.start = time.start
            this.form.end = time.end
            this.form.altstart = time.altstart
            this.form.altend = time.altend
            this.editMode = true

            $('#exampleModal').modal('show')
        },
        addTime() {
            this.form.reset()
            this.editMode = false
        },

        removeTime(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "If time frame is being used in the timetable, all periods allocated will be deleted also?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove time frame!'
            }).then((result) => {
                if (result.value) {

                    this.form.delete(this.$route('periods.destroy',id),{
                        preserveState:true,
                        onSuccess: ()=> {
                            Swal.fire(
                                'Successful!',
                                'Time was removed',
                                'success'
                            )

                        }
                    })

                }
            })
        },

        createTime() {
            this.form.post(this.$route('periods.store'),{
                preserveState:true,
                onSuccess: ()=> {
                    $('#exampleModal').modal('hide')
                    Swal.fire(
                        'Created!',
                        'Time Frame added',
                        'success',
                    )

                }
            })

        },
        updateTime(){
            this.form.put(this.$route('periods.update',this.form.id),{
                preserveState:true,
                onSuccess: ()=> {
                    $('#exampleModal').modal('hide');

                    Swal.fire (
                        'Updated!',
                        'Time Frame updated',
                        'success',
                    );
                }
            })
        },
    },
}
</script>

<style scoped>

</style>
