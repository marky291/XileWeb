@php use Carbon\Carbon; @endphp

    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/>
    <title>XileRO Patcher</title>
</head>

    <body>

        <style>
            * {
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }

            body {
                margin-top: 0 !important;
                padding-top: 0 !important;
                background-repeat: no-repeat;
                background-attachment: fixed;
                background-color: #000000;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 12px;
                color: #E6C17B;
                background-position: center top;
                background-image: url('{{ asset('assets/patcher/background.jpg') }}');
            }

            h5 {
                padding: 15px 0px;
                border-bottom: 1px solid #524a3a;
            }

            ul {
                padding-left: 0px;
                margin-left: 7px;
                margin-top: 10px;
                list-style: none;
            }

            a {
                color: #f7b948;
                text-decoration: none;
            }

            span {
                color: #e7c585;
                text-decoration: underline;
                margin-left: 10px;
                font-weight: normal;
            }

            li {
                margin-top: 5px;
                margin-bottom: 5px;
                font-weight: bold;
            }

            h4 {
                font-size: 12px;
                margin-bottom: 1px;
            }

            .month {
                border-bottom: 1px solid #524a3a;
            }

            .description {
                margin-left: 25px;
                color: #e7d7b8;
                font-weight: normal;
            }
        </style>

        <h5>Always run your Patcher! <3</h5>
        <div>
            <div class="month">
                @foreach($groupedPosts as $date => $posts)
                    <h4>{{ $date }}</h4>
                    <ul>
                        @foreach($posts as $post)
                            <li>
                                <a target="_blank" href="{{ route('posts.show', $post->slug) }}">
                                    {{ Carbon::parse($post->created_at)->format('d') }} <span>{{ $post->title }}</span>
                                    <div class="description">
                                        <p>{{ $post->patcher_notice }}</p>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        </div>
    </body>
</html>