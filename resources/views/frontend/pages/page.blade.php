@extends('frontend.layouts.app')

@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('frontend.home') }}">Home</a></li>
                    <li class="breadcrumb-item">{{ $page->name }}</li>
                </ol>
            </div>
        </div>
    </section>

    @if ($page->slug == 'contact-us')
        <section class=" section-10">
            <div class="container">
                <div class="section-title mt-5 ">
                    <h2>{{ $page->name }}</h2>
                </div>
            </div>
        </section>

        <section>
            <div class="container">
                <div class="row">
                    <div class="col-md-6 mt-3 pe-lg-5">
                        {{-- <p>It is a long established fact that a reader will be distracted by the readable content of a page
                            when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal
                            distribution of letters, as opposed to using Content.</p>
                        <address>
                            Cecilia Chapman <br>
                            711-2880 Nulla St.<br>
                            Mankato Mississippi 96522<br>
                            <a href="tel:+xxxxxxxx">(XXX) 555-2368</a><br>
                            <a href="mailto:jim@rock.com">jim@rock.com</a>
                        </address> --}}
                        {!! $page->content !!}
                    </div>

                    <div class="col-md-6">
                        <form class="shake" role="form" method="post" id="contactForm" name="contact-form">
                            @csrf
                            <div class="mb-3">
                                <label class="mb-2" for="name">Name</label>
                                <input class="form-control" id="name" type="text" name="name" required
                                    data-error="Please enter your name">
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="mb-3">
                                <label class="mb-2" for="email">Email</label>
                                <input class="form-control" id="email" type="email" name="email" required
                                    data-error="Please enter your Email">
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="mb-3">
                                <label class="mb-2">Subject</label>
                                <input class="form-control" id="msg_subject" type="text" name="subject" required
                                    data-error="Please enter your message subject">
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="mb-2">Message</label>
                                <textarea class="form-control" rows="3" id="message" name="message" required data-error="Write your message"></textarea>
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-submit">
                                <button class="btn btn-dark" type="submit" id="form-submit"><i
                                        class="material-icons mdi mdi-message-outline"></i> Send Message</button>
                                <div id="msgSubmit" class="h3 text-center hidden"></div>
                                <div class="clearfix"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    @else
        <section class=" section-10">
            <div class="container">
                <h1 class="my-3">{{ $page->name }}</h1>

                {!! $page->content !!}

            </div>
        </section>
    @endif
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#contactForm').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: "{{ route('contact.send') }}",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Success!', response.message, 'success');
                            window.location.reload();

                        } else {
                            Swal.fire('Error!', 'There was a problem sending your message.',
                                'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'There was a problem sending your message.',
                            'error');
                    }
                });
            });
        });
    </script>
@endsection
