<div
  x-data="{ show: true }"
  x-show="show"
  x-init="setTimeout(() => show = false, 3000)"
  class="rounded-xl border border-success-500 bg-success-50 p-4 dark:border-success-500/30 dark:bg-success-500/15"
  x-transition:enter="transition ease-out duration-500 transform"
  x-transition:enter-start="opacity-0 scale-95"
  x-transition:enter-end="opacity-100 scale-100"
  x-transition:leave="transition ease-in duration-300 transform"
  x-transition:leave-start="opacity-100 scale-100"
  x-transition:leave-end="opacity-0 scale-95"
>
  <div class="flex items-start gap-3">
    <div class="-mt-0.5 text-success-500">
      <svg
        class="fill-current"
        width="24"
        height="24"
        viewBox="0 0 24 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          fill-rule="evenodd"
          clip-rule="evenodd"
          d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM10 16L5 11L6.41 9.59L10 13.17L17.59 5.59L19 7L10 16Z"
          fill="#4CAF50"
        />
      </svg>
    </div>

    <div>
      <h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">
        Success Message
      </h4>

      <p class="text-sm text-gray-500 dark:text-gray-400">
        {{ $message }}
      </p>
    </div>
  </div>
</div>
