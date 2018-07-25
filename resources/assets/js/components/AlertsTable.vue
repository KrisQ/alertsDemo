<template>
	<div class="row">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header">
					<h2>Alerts</h2>
				</div>
				<div class="card-body">
					<div class="row">

						<div class="col-md-8">
							<div class="dropdown">
								<button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Table Size: {{pageSize}}
								</button>
								<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
									<li><a class="dropdown-item" href="#" @click="updateTableSize(5)">5</a></li>
									<li><a class="dropdown-item" href="#" @click="updateTableSize(10)">10</a></li>
									<li><a class="dropdown-item" href="#" @click="updateTableSize(25)">25</a></li>
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<input type="text" class="form-control" placeholder="Search Table" @input="updateAlertList" v-model="search">
							</div>
						</div>

					</div>

					<div v-if="message !== ''" @click="fetchAlerts()" class="alert alert-info" role="alert" v-text="message"></div>
					<table class="table table-bordered datatables" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc">
						<thead>
							<tr>
								<th @click="updateSortBy('customer')">Customer <span v-if="sortBy == 'customer'" :class="[sortDesc === false ? 'glyphicon glyphicon-arrow-up' : 'glyphicon glyphicon-arrow-down']" aria-hidden="true"></span></th>
								<th @click="updateSortBy('date_range')">Date Range <span v-if="sortBy == 'date_range'" :class="[sortDesc === false ? 'glyphicon glyphicon-arrow-up' : 'glyphicon glyphicon-arrow-down']" aria-hidden="true"></span></th>
								<th @click="updateSortBy('short_description')">Short Description <span v-if="sortBy == 'short_description'" :class="[sortDesc === false ? 'glyphicon glyphicon-arrow-up' : 'glyphicon glyphicon-arrow-down']" aria-hidden="true"></span></th>
								<th>Select</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="alert in visibleAlerts" :key="alert.id" :visibleAlerts="visibleAlerts" :currentPage="currentPage">
								<td><p v-for="customer in customers" :key="customer.id" :hidden="customer.id !== alert.customer_id">{{customer.name}}</p></td>
								<td>{{alert.date_range}}</td>
								<td :class="[alert.status === 1 ? 'text-danger' : 'text-success']">{{alert.short_description}}</td>
								<td><button @click="changeCurrentAlert(alert)" class="btn btn-info btn-block">Select</button></td>
							</tr>
						</tbody>
					</table>
					<nav aria-label="...">
					  <ul class="pagination">
						<li :class="[{hidden: currentPage == 0}]" class="page-item">
					      <a class="page-link" href="#" @click="updatePage(currentPage - 1)" tabindex="-1">Previous</a>
					    </li>
					    <li class="page-item disabled text-dark">
					    	<a class="page-link text-dark" href="">Page {{currentPage + 1}} of {{lastPage + 1}}</a>
					    </li>
					    <!-- <li :class="[{disabled: !pagination.next_page_url}]" class="page-item"> -->
						<li :class="[{hidden: currentPage == lastPage}]" class="page-item">
					      <a class="page-link" @click="updatePage(currentPage + 1)" href="#">Next</a>
					    </li>
					  </ul>
					</nav>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="card">
				<div class="card-header">
					<h2>Alert</h2>
				</div>
				<div class="card-body">
					<button :class="[{hidden: this.edit === false}]" class="btn btn-info btn-block" @click="createAlertIsTrue()">Add New Alert</button>
					<br>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Short Description" v-model="alert.short_description">
					</div>
					<div class="form-group">
						<textarea class="form-control" placeholder="Long Description" v-model="alert.long_description"></textarea>
					</div>
					<div class="form-group">
						<input type="date" class="form-control" placeholder="Short Description" v-model="alert.date_range">
					</div>
					<div class="form-group">
						<select class="form-control" v-model="alert.status">
							<option :selected="alert.status === 1" value="1">Active</option>
							<option :selected="alert.status === 2" value="2">Innactive</option>
						</select>
					</div>
					<div class="form-group">
						<select class="form-control" v-model="alert.customer_id">
							<option v-for="customer in customers" :key="customer.id" :selected="customer.id === alert.customer_id" :value="customer.id">{{customer.name}}</option>
						</select>
					</div>
					<div :hidden="this.edit === true">
						<button @click="createAlert()" class="btn btn-block btn-info">Create</button>
					</div>
					<div :hidden="this.edit === false">
						<div class="row">
							<div class="col-md-6"><button @click="updateAlert(alert)" class="btn btn-block btn-info">Edit</button></div>
							<div class="col-md-6"><button @click="deleteAlert(alert.id)" class="btn btn-block btn-info">Delete</button></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				alerts: [],
				resetedAlerts: [],
				visibleAlerts: [],
				sortBy: 'id',
      			sortDesc: false,
				alert : {
					id: '',
					customer_id: '',
					user_id: '',
					short_description: '',
					long_description: '',
    				status: ''
				},
				customers: [],
				user: '',
				alert_id: '',
				edit: false,
                message: '',
				currentPage: 0,
				lastPage: 0,
				pageSize: 5,
				search:''
			}
		},
		created(){
			this.fetchAlerts();
		},
		methods: {
			fetchAlerts(page_url){
				this.message = '';
				let vm = this;
				page_url = page_url || 'api/alert/all'
				axios.get(page_url)
					  .then(response => {
					  	this.user = response.data.user;
						this.alerts = response.data.alerts;
						this.resetedAlerts = response.data.alerts;
						this.customers = response.data.customers;
						this.updateVisibleAlerts();
					    // console.log(response.data.alerts.current_page);
					   	// vm.makePagination(response.data.alerts)
					  })
					  .catch(error => console.log(error));
            },

            updateVisibleAlerts() {
				let vm = this;
				if(this.sortBy === 'id'){
					if(this.sortDesc === true){
						this.alerts = this.alerts.sort((a,b) => {
							return a[1] - b[1];
						});
					} else {
						this.alerts = this.alerts.reverse((a,b) => {
							return a[1] - b[1];
						});
					}
				} else {
					if(this.sortDesc === true){
						this.alerts = this.alerts.sort(function(a, b) {
							if (a[vm.sortBy] < b[vm.sortBy]) {
								return -1;
							}
							if (a[vm.sortBy] > b[vm.sortBy]) {
								return 1;
							}
							// names must be equal
							return 0;
						});
					} else {
						this.alerts = this.alerts.reverse(function(a, b) {
							if (a[vm.sortBy] < b[vm.sortBy]) {
								return -1;
							}
							if (a[vm.sortBy] > b[vm.sortBy]) {
								return 1;
							}
							// names must be equal
							return 0;
						});
					}
				}
				if(this.search != ''){
					let val = this.search;
					this.alerts = this.alerts.filter((alert) => {
						return alert.short_description.includes(val) || alert.long_description.includes(val);
					});
				}
                this.visibleAlerts = this.alerts.slice(this.currentPage * this.pageSize, (this.currentPage * this.pageSize) + this.pageSize);
                if (this.visibleAlerts.length == 0 && this.currentPage > 0){
                    this.updatePage(this.currentPage - 1);
				}
				this.lastPage = Math.trunc(this.alerts.length / this.pageSize);
            },

            updatePage(pageNumber){
                this.currentPage = pageNumber;
				this.updateVisibleAlerts();

            },
			changeCurrentAlert(alert){
				this.alert = alert;
				this.edit = true;
				this.alert_id = alert.id;
			},

			updateSortBy(sort){
				if(this.sortBy === sort){
					this.sortDesc = this.sortDesc === true ? false : true;
				} else {
					this.sortBy = sort;
				}
				this.updateVisibleAlerts();

			},

			updateAlertList(){
				let val = this.search;
				this.alerts = this.alerts.filter((alert) => {
					return alert.short_description.includes(val) || alert.long_description.includes(val);
				});
				this.updateVisibleAlerts();
				this.resetAlerts();
			},
			resetAlerts(page_url){
				this.alerts = this.resetedAlerts;
			},
			updateTableSize(tableSize){
				this.pageSize = tableSize;
				this.updateVisibleAlerts();
			},
            
			createAlertIsTrue(){
				this.edit = false;
				this.alert = {
					id: '',
					customer_id: '',
					user_id: '',
					short_description: '',
					long_description: '',
    				status: ''
				}
            },
            
			createAlert(){
				axios.post('api/alert/create', {
					customer_id: this.alert.customer_id,
					user_id: this.user.id,
					short_description: this.alert.short_description,
					long_description: this.alert.long_description,
					date_range: this.alert.date_range,
					status: this.alert.status
				})	
				.then(data => {
					this.createAlertIsTrue()
					this.fetchAlerts();
					this.updateVisibleAlerts();

				})
				.catch(error => {
					console.log(error);
					this.message = "All Fields are required"
				});
            },
            
			updateAlert(){
				axios.post('api/alert/update', {
						id: this.alert.id,
						customer_id: this.alert.customer_id,
						user_id: this.user.id,
						short_description: this.alert.short_description,
						long_description: this.alert.long_description,
						date_range: this.alert.date_range,
						status: this.alert.status
					})	
					.then(data => {
						this.createAlertIsTrue()
						this.fetchAlerts();
						this.updateVisibleAlerts();
					})
					.catch(error => {
						console.log(error);
						this.message = "All Fields are required"
					});
            },
            
			deleteAlert(id){
					axios.delete(`api/alert/${id}/delete`)
						.then(response => {
							this.createAlertIsTrue()
							this.fetchAlerts();
							this.updateVisibleAlerts();
						})
						.catch(error => console.log(error));
			}
		},
	}
</script>