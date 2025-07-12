<div>

    <!--[if BLOCK]><![endif]--><?php if($paginator->hasPages()): ?>

        <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between">

            <span>

                

                <!--[if BLOCK]><![endif]--><?php if($paginator->onFirstPage()): ?>

                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">

                        <?php echo __('pagination.previous'); ?>


                    </span>

                <?php else: ?>

                    <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">

                        <?php echo __('pagination.previous'); ?>


                    </button>

                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            </span>

 

            <span>

                

                <!--[if BLOCK]><![endif]--><?php if($paginator->hasMorePages()): ?>

                    <button wire:click="nextPage" wire:loading.attr="disabled" rel="next" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">

                        <?php echo __('pagination.next'); ?>


                    </button>

                <?php else: ?>

                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">

                        <?php echo __('pagination.next'); ?>


                    </span>

                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            </span>

        </nav>

    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

</div><?php /**PATH C:\laragon\www\FlashWall\resources\views/vendor/livewire/tailwind.blade.php ENDPATH**/ ?>