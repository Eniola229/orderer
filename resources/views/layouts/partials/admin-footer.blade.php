<footer class="footer">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="fs-11 text-muted fw-medium text-uppercase mb-0">
                    <span>Copyright &copy;</span>
                    <script>document.write(new Date().getFullYear());</script>
                    <span class="ms-1">Orderer Admin Panel. All rights reserved.</span>
                </p>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center justify-content-md-end gap-3 mt-3 mt-md-0">
                    <span class="fs-11 text-muted">
                        Logged in as:
                        <strong>{{ str_replace('_', ' ', ucwords(auth('admin')->user()->role)) }}</strong>
                    </span>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="{{ asset('dashboard/assets/vendors/js/vendors.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/vendors/js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/vendors/js/apexcharts.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/vendors/js/circle-progress.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/common-init.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/dashboard-init.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/theme-customizer-init.min.js') }}"></script>

@stack('scripts')
</body>
</html>