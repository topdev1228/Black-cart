<html class="h-full">
<head>
    @vite(['resources/js/index.css'])
</head>
<body class="bg-white-900 flex h-full">
<div class="max-w-[50rem] flex flex-col mx-auto w-full h-full">
    <!-- ========== HEADER ========== -->
    <header class="mb-auto flex flex-wrap sm:justify-start sm:flex-nowrap z-50 w-full text-sm py-4">
        <nav class="w-full px-4 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8" aria-label="Global">
            <div class="flex items-center justify-between">
                <a class="flex-none text-xl font-semibold text-black focus:outline-none focus:ring-1 focus:ring-gray-600"
                   href="#" aria-label="Brand">Blackcart</a>
                <div class="sm:hidden">
                    <button type="button"
                            class="hs-collapse-toggle p-2 inline-flex justify-center items-center gap-2 rounded-lg border border-gray-700 hover:border-gray-600 font-medium text-gray-800 hover:text-black shadow-sm align-middle focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 focus:ring-blue-600 transition-all text-sm"
                            data-hs-collapse="#navbar-collapse-with-animation"
                            aria-controls="navbar-collapse-with-animation" aria-label="Toggle navigation">
                        <svg class="hs-collapse-open:hidden flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                             width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" x2="21" y1="6" y2="6"/>
                            <line x1="3" x2="21" y1="12" y2="12"/>
                            <line x1="3" x2="21" y1="18" y2="18"/>
                        </svg>
                        <svg class="hs-collapse-open:block hidden flex-shrink-0 w-4 h-4"
                             xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/>
                            <path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div id="navbar-collapse-with-animation"
                 class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow sm:block">
                <div class="flex flex-col gap-5 mt-5 sm:flex-row sm:items-center sm:justify-end sm:mt-0 sm:ps-5">
                </div>
            </div>
        </nav>
    </header>
    <!-- ========== END HEADER ========== -->

    <!-- ========== MAIN CONTENT ========== -->
    <main id="content" role="main">
        <div class="text-center py-10 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">
            <h1 class="block text-2xl font-bold text-black sm:text-4xl">Blackcart</h1>
            <p class="mt-3 text-lg text-gray-800">What ending your trial means:</p>
            <ol class="list-decimal list-outside pl-[revert] text-center">
                <li class="text-left space-x-6">
                    <span class="text-gray-800 dark:text-gray-800 text-left">
                    Your trial will end immediately.
                  </span>
                </li>
                <li class="text-left space-x-2">
                    <span class="text-gray-800 dark:text-gray-800 text-left">
                    All unpaid items, whether they have been delivered or are on their way to you, will be charged to your provided payment method.
                  </span>
                </li>
                <li class="text-left space-x-2">
                    <span class="text-gray-800 dark:text-gray-800 text-left">
                    Don't worry, you can still return the items after you end your trial.
                  </span>
                </li>
            </ol>
            <div class="mt-5 flex flex-col justify-center items-center gap-2 sm:flex-row sm:gap-3">
                <button onclick="toggleModal('confirm-cancel')" data-modal-target="popup-modal"
                        data-modal-toggle="popup-modal"
                        class="w-full sm:w-auto py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-black text-gray-300 hover:bg-gray-200 hover:text-gray-900 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                        href="#">
                    End my Trial
                </button>
            </div>
        </div>
    </main>
    <!-- ========== END MAIN CONTENT ========== -->
    <!-- ========== MODAL ========== -->

    <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
         id="confirm-cancel">
        <div class="relative w-auto my-6 mx-auto max-w-3xl">
            <!--content-->
            <div class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
                <!--header-->
                <div class="flex items-start justify-between p-5 border-b border-solid border-blueGray-200 rounded-t">
                    <h3 class="text-3xl font-semibold">
                        Are you sure?
                    </h3>
                    <button class="p-1 ml-auto bg-transparent border-0 text-black opacity-5 float-right text-3xl leading-none font-semibold outline-none focus:outline-none"
                            onclick="toggleModal('confirm-cancel')">
          <span class="bg-transparent text-black opacity-5 h-6 w-6 text-2xl block outline-none focus:outline-none">
            ×
          </span>
                    </button>
                </div>
                <!--body-->
                <div class="relative p-6 flex-auto">
                    <p class="my-4 text-blueGray-500 text-lg leading-relaxed">
                        The unpaid balance on your try-before-you-buy item(s) will be charged to the provided payment
                        method.
                    </p>
                </div>
                <!--footer-->
                <div class="flex items-center justify-end p-6 border-t border-solid border-blueGray-200 rounded-b">
                    <button class="text-red-500 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150"
                            type="button" onclick="toggleModal('confirm-cancel')">
                        Nevermind
                    </button>
                    <button class="bg-red-500 text-white active:bg-red-600 font-bold uppercase text-sm px-6 py-3 rounded shadow hover:shadow-lg outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150"
                            type="button" onclick="submit()">
                        Yes, End my Trial
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="hidden opacity-50 fixed inset-0 z-40 bg-black" id="confirm-cancel-backdrop"></div>
    <script type="text/javascript">
        function toggleModal(modalID) {
            document.getElementById(modalID).classList.toggle("hidden");
            document.getElementById(modalID + "-backdrop").classList.toggle("hidden");
            document.getElementById(modalID).classList.toggle("flex");
            document.getElementById(modalID + "-backdrop").classList.toggle("flex");
        }

        function closeModal(modalID) {
            document.getElementById(modalID).classList.add("hidden");
            document.getElementById(modalID + "-backdrop").classList.add("hidden");
            document.getElementById(modalID).classList.remove("flex");
            document.getElementById(modalID + "-backdrop").classList.remove("flex");
        }

        function submit() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "{{ $formUrl }}", true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader("Authorization", "Bearer {{ $jwt }}");
            xhr.addEventListener('load', function () {
                closeModal('confirm-cancel');
                var responseObject = JSON.parse(this.response);
                if (responseObject.status == 'success') {
                    setContent("Your trial has ended.<br>You should be receiving a receipt email shortly." +
                        "<br><br>Thank you for trying with Blackcart!");
                } else {
                    setContent("An error occurred. This issue has been logged. " +
                        "No further action is required on your part.");
                }
            });
            xhr.send();
        }

        function setContent(newContent) {
            var content = document.getElementById('content');
            content.innerHTML = newContent;
        }
    </script>
    <!-- ========== END MODAL ========== -->

    <!-- ========== FOOTER ========== -->
    <footer class="mt-auto text-center py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        </div>
    </footer>
    <!-- ========== END FOOTER ========== -->
</div>
</body>
</html>
