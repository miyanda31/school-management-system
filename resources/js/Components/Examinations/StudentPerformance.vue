<template>
    <div class="col-md-6">
        <div class="card-box pd-20 height-100-p mb-30">
            <div class="row">
                <div class="col-md-6">
                    <multiselect @searchable="true" @search-change="loadUser" placeholder="Search Student" v-model="user" :options="users" track-by="id" label="name" />
                </div>
                <div class="col-md-6">
                    <label>
                        <select @change="loadCategory" v-model="start"  class="form-control form-control-sm">
                            <option disabled value="">Term</option>
                            <option v-for="y in user.scores" :value="y.term_id">T{{y.term.number}} - {{y.term.year}}</option>
                        </select>
                    </label>
                    <label>
                        <select @change="loadCategory"  v-model="end"  class="form-control form-control-sm">
                            <option disabled value="">Term</option>
                            <option v-for="y in user.scores" :value="y.term_id">T{{y.term.number}} - {{y.term.year}}</option>
                        </select>
                    </label>
                </div>
            </div>
            <div class="clearfix"></div>
            <line-chart :loading="loading" v-if="data.length>0" :data="data" empty="No data selected" ></line-chart>
            <length :category="data" message="No data loaded, please make a selection"/>
        </div>
    </div>
</template>

<script>
export default {
    name: "StudentPerformance",
    data() {
        return {
            data: [],
            users:[],
            terms:[],
            user:{},
            start:'',
            end:'',
            loading:false
        }
    },
    methods: {
        loadUser(query) {
            if (query) {
                this.loading = true
                axios.get(this.$route('analysis.show',query)).then(({data})=>{
                    this.users = data
                    this.loading = true
                });
            }
        },
        loadCategory() {

        }
    },
}
</script>

<style scoped>

</style>
