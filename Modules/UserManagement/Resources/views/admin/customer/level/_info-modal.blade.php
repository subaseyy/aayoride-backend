<div class="modal fade" id="getInformationModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="getInformationModal"
        aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0 d-flex justify-content-end">

                <button type="button" class="btn-close btn-sm shadow-none border-0" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body px-4 pt-0 pb-5">
                <div class="swiper instruction-carousel pb-3">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide px-4">
                            <div class="d-flex flex-column align-items-center gap-2 text-center">
                                <img width="80" class="mb-3" src="{{asset('public/assets/admin-module/img/avatar/avatar.svg')}}"
                                    loading="lazy" alt />
                                <h5 class="lh-md mb-3 text-capitalize">{{ translate("What’s default level") }}?</h5>
                                <p>{{ translate("When a new customer logs into the app, they start at a default level. This level helps new customers get used to the platform. As the customer completes rides and meets specific performance goals set by the admin, they can move up to the next level.") }}</p>
                            </div>
                        </div>
                        <div class="swiper-slide px-4">
                            <div class="d-flex flex-column align-items-center gap-2 text-center">
                                <img width="80" class="mb-3" src="{{asset('public/assets/admin-module/img/avatar/avatar.svg')}}"
                                    loading="lazy" alt />
                                <h5 class="lh-md mb-3 text-capitalize">{{ translate("How to setup level") }}?</h5>
                                <p>{{ translate("Using 'Add New Level' the admin can create a new level for customers. To set up a new level, the admin needs to define targets and points to be earned. When a customer meets those targets and earns the required points, they will upgrade to the new level from their current one.") }}</p>
                            </div>
                        </div>
                        <div class="swiper-slide px-4">
                            <div class="d-flex flex-column align-items-center gap-2 text-center">
                                <img width="80" class="mb-3" src="{{asset('public/assets/admin-module/img/avatar/avatar.svg')}}"
                                    loading="lazy" alt />
                                <h5 class="lh-md mb-3 text-capitalize">
                                    {{translate("What’s priority & how it effects")}}?
                                </h5>
                                <p>{{ translate("When creating a level, need to prioritize defining the targets before the customer uses this level and points required for a customer to achieve that level. If there is a mismatch in setting these criteria, it may affect the customer's ability to reach the level.") }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>

                <div class="instruction-pagination-custom mb-2"></div>
                <div class="swiper-pagination instruction-pagination"></div>
            </div>
        </div>
    </div>
</div>
