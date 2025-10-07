<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>interface</title>

        <link rel="icon" href="{{ asset('img/lotti.jpeg') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        
        <style>
            /* 
                アクセントカラー：#859A93
                ベースカラー：#FFFCF7
                テキストカラー：#544739
            */
            @import url('https://fonts.googleapis.com/css?family=Noto+Serif+JP');
            body {
                font-family: 'Noto Serif JP', sans-serif;
                background-color: #FFFCF7;
            }
            body, input {
                color: #544739;
            }
            p,h1,h2,h3,h4 {
                margin: 0;
            }
            section {
                max-width: 80%;
                margin: 0 auto 2rem;
                padding: 1rem 2rem 1.5rem;
                border-radius: 15px;
            }
            div {
                margin-left: 1.5rem;
            }
            input{
                border: none;
            }

            .title{
                text-align: center;
                margin-bottom: 2rem;
            }
            
             /* 2. Neumorphism */
            .neumorphism {
                background-color: rgb(133, 154, 147);
            }
            .neumorphism .box {
                border-radius: 6px;
            }
            .neumorphism .box, .neumorphism .circle {
                background-color: rgb(133, 154, 147);
                transition: box-shadow 0.5s;
                box-shadow:
                2px 2px 6px rgba(18, 47, 61, 0.5),
                -2px -2px 6px rgba(191, 195, 186, 0.9),
                inset 2px 2px 6px transparent,
                inset -2px -2px 6px transparent;
                height: 1.6rem;
            }
            .neumorphism .box:hover, .neumorphism .circle:hover {
                box-shadow:
                2px 2px 6px transparent,
                -2px -2px 6px transparent,
                inset 2px 2px 6px rgba(18, 47, 61, 0.5),
                inset -2px -2px 6px rgba(191, 195, 186, 0.9);
            }

            .input_text {
                width: 20rem;
                outline: none;
                padding-left: 2em;
                margin-right: 1rem;
                border: none;
                border-bottom: 1px solid #585c63;
                background: transparent;
            }
        </style>
    </head>

    <body>
        <main>
            <form action="{{ route('trial.interface.store') }}" method="POST" enctype="multipart/form-data" >
                {{ csrf_field() }}
                <h2 class="title">tests Studio</h2>

                <section class="group neumorphism">
                    <h3 class="title">割引のインターフェース</h4>
                    <div>
                        <p>1000円以上で10%。1000未満で5%の割引</p>
                        <input type="text" name="amount" class="input_text" placeholder="例：2000">
                        <input type="submit" class="box">
                        <p>割引額：@if (!empty($discount)){{ $discount }}円 @endif</p>
                    </div>
                </section>

                <section class="group neumorphism">
                    <h3 class="title">ポイント返還率</h3>
                    <div>
                        <p>あなたの会員は{{ $userRank }}です。ポイント還元率{{ $userPoint }}になります</p>

                        <p>ご希望の会員ランクはございますか？</p>
                        <select name="memberRank">
                        @foreach($member_ranks as $rank)
                            <option value="{{ $rank }}">{{ $rank }}</option>
                        @endforeach
                        </select>
                    </div>
                </section>

            </form>
        </main>
    </body>
</html>
