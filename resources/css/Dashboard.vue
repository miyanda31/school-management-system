<template>
<Layout >
    <div class="row">
        <div class="col-xl-3 mb-30 ">
            <div class="card-box p-4 height-100-p widget-style1">
                <div class="d-flex flex-wrap align-items-center">
                    <div class="progress-data">
                        <span class="fa fa-users fa-3x text-success"></span>
                    </div>
                    <div class="widget-data">
                        <div class="h4 mb-0">{{users[1].total}}</div>
                        <div class="weight-600 font-14">Students</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 mb-30">
            <div class="card-box p-4 height-100-p widget-style1">
                <div class="d-flex flex-wrap align-items-center">
                    <div class="progress-data">
                        <span class="fa fa-user fa-3x text-warning"></span>
                    </div>
                    <div class="widget-data">
                        <div class="h4 mb-0">{{users[0].total}}</div>
                        <div class="weight-600 font-14">Teachers</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 mb-30">
            <div class="card-box p-4 height-100-p widget-style1">
                <div class="d-flex flex-wrap align-items-center">
                    <div class="progress-data">
                        <span class="fa fa-book fa-3x text-secondary"></span>
                    </div>
                    <div class="widget-data">
                        <div class="h4 mb-0">{{bookStats[1].total}}</div>
                        <div class="weight-600 font-14">Books Available</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 mb-30">
            <div class="card-box p-4 height-100-p widget-style1">
                <div class="d-flex flex-wrap align-items-center">
                    <div class="progress-data">
                        <span class="fa fa-book fa-3x text-primary"></span>
                    </div>
                    <div class="widget-data">
                        <div class="h4 mb-0">{{bookStats[0].total}}</div>
                        <div class="weight-600 font-14">Books Loaned</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-xl-8 mb-30">
            <div class="card-box height-100-p pd-20">
                <h2 class="h4 mb-20">Monthly Stats</h2>
                <column-chart :data="annualStats" :download="true"></column-chart>
            </div>
        </div>
        <div class="col-xl-4 mb-30">
            <div class="card-box height-100-p pd-20">
                <h2 class="h4 mb-20">This Month</h2>

                <pie-chart :download="true" :data="stats" :donut="true" :legend="true" lengend="bottom"></pie-chart>
            </div>
        </div>
    </div>

</Layout>
</template>

<script>
export default {
    name: "Dashboard",
    props: {
        stats:[],
        annualStats:[],
        users:[],
        bookStats:[]
    }
}
</script>

