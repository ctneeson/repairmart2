<!-- Find a listing form -->
<section class="find-a-listing">
    <div class="container">
        <form
        action="{{ route('listings.search') }}"
        method="GET"
        class="find-a-listing-form card flex p-medium"
        >
            <div class="find-a-listing-inputs">
                <div>
                    <select id="productCategorySelect" name="product_category_id">
                        <option value="">Product Type</option>
                        <option value="1">Arts, Crafts & Sewing</option>
                        <option value="2">Audio-Visual</option>
                        <option value="3">Beauty & Personal Care</option>
                        <option value="4">Camera & Photo</option>
                        <option value="5">Cell Phones & Accessories</option>
                        <option value="6">Computers & Accessories</option>
                        <option value="7">eBook Readers & Accessories</option>
                        <option value="8">Heating, Cooling & Air Quality</option>
                        <option value="9">Home Electronics</option>
                        <option value="10">Industrial & Scientific</option>
                    </select>
                </div>
                <div>
                    <select id="productSubCategorySelect" name="product_subcategory_id">
                        <option value="" style="display: block">Product SubType</option>
                        <option value="1" data-parent="2">Audio Headphones & Accessories</option>
                        <option value="2" data-parent="2">Cassette Players & Recorders</option>
                        <option value="3" data-parent="2">CD Players</option>
                        <option value="4" data-parent="2">MP3 & MP4 Players</option>
                        <option value="5" data-parent="2">Home Theater Systems</option>
                        <option value="6" data-parent="2">Other-Misc.</option>
                    </select>
                </div>
                <div>
                    <select id="manufacturerSelect" name="manufacturer_id">
                        <option value="">Manufacturer</option>
                        <option value="1">
                        Apple
                        </option>
                        <option value="2">
                        Asus
                        </option>
                        <option value="3">
                        Blaupunkt
                        </option>
                        <option value="4">
                        Bosch
                        </option>
                        <option value="5">
                        Braun
                        </option>
                        <option value="6">
                        Casio
                        </option>
                        <option value="7">
                        Dyson
                        </option>
                        <option value="8">
                        Hisense
                        </option>
                        <option value="9">
                        HP
                        </option>
                        <option value="10">
                        Huawei
                        </option>
                        <option value="11">
                            Hitachi
                        </option>
                        <option value="12">
                            IBM
                        </option>
                        <option value="13">
                            JVC
                        </option>
                        <option value="14">
                            Kenwood
                        </option>
                        <option value="15">
                            Kyocera
                        </option>
                        <option value="16">
                            Lenovo
                        </option>
                        <option value="17">
                        LG
                        </option>
                        <option value="18">
                            Marshall
                        </option>
                        <option value="19">
                            Microsoft
                        </option>
                        <option value="20">
                            Miele
                        </option>
                        <option value="21">
                            Mitsubishi
                        </option>
                        <option value="22">
                            Morphy Richards
                        </option>
                        <option value="23">
                            Motorola
                        </option>
                        <option value="24">
                            Nikon
                        </option>
                        <option value="25">
                            Nintendo
                        </option>
                        <option value="26">
                            Nokia
                        </option>
                        <option value="27">
                            Olivetti
                        </option>
                        <option value="28">
                            Olympus
                        </option>
                        <option value="29">
                            OnePlus
                        </option>
                        <option value="30">
                            Panasonic
                        </option>
                        <option value="31">
                            Philips
                        </option>
                        <option value="32">
                            Qualcomm
                        </option>
                        <option value="33">
                            Realtek
                        </option>
                        <option value="34">
                            Russell Hobbs
                        </option>
                        <option value="35">
                            Samsung
                        </option>
                        <option value="36">
                            Sennheiser
                        </option>
                        <option value="37">
                            Sharp
                        </option>
                        <option value="38">
                            Siemens
                        </option>
                        <option value="39">
                            Sony
                        </option>
                        <option value="40">
                            TDK
                        </option>
                        <option value="41">
                            Texas Instruments
                        </option>
                        <option value="42">
                            Thomson
                        </option>
                        <option value="43">
                            Toshiba
                        </option>
                        <option value="44">
                            TP-Link/intex
                        </option>
                        <option value="45">
                            Western Digital
                        </option>
                        <option value="46">
                            Wortmann
                        </option>
                        <option value="47">
                            Xerox
                        </option>
                        <option value="48">
                            Xiaomi
                        </option>
                        <option value="49">
                            ZTE
                        </option>
                    </select>
                </div>
                <div>
                    <select id="stateSelect" name="state_id">
                        <option value="">State/Region</option>
                        <option value="4">California</option>
                        <option value="2">Kansas</option>
                        <option value="1">Ohio</option>
                        <option value="5">Oregon</option>
                    </select>
                </div>
                <div>
                    <select id="citySelect" name="city_id">
                        <option value="" style="display: block">City</option>
                        <option value="3" data-parent="1" style="display: none">
                        Carmelstad
                        </option>
                        <option value="8" data-parent="2" style="display: none">
                        Cormierville
                        </option>
                        <option value="14" data-parent="3" style="display: none">
                        Dareville
                        </option>
                        <option value="13" data-parent="3" style="display: none">
                        Demarcotown
                        </option>
                        <option value="10" data-parent="2" style="display: none">
                        Doylebury
                        </option>
                        <option value="18" data-parent="4" style="display: none">
                        East Alfonso
                        </option>
                        <option value="9" data-parent="2" style="display: none">
                        East Ladarius
                        </option>
                        <option value="23" data-parent="5" style="display: none">
                        Kelvinmouth
                        </option>
                        <option value="24" data-parent="5" style="display: none">
                        Kemmerchester
                        </option>
                        <option value="25" data-parent="5" style="display: none">
                        Kunzeview
                        </option>
                        <option value="6" data-parent="2" style="display: none">
                        Lake Kelsi
                        </option>
                        <option value="16" data-parent="4" style="display: none">
                        Larsonview
                        </option>
                        <option value="2" data-parent="1" style="display: none">
                        Lindstad
                        </option>
                        <option value="5" data-parent="1" style="display: none">
                        Loganshire
                        </option>
                        <option value="15" data-parent="3" style="display: none">
                        Maximilliaberg
                        </option>
                        <option value="7" data-parent="2" style="display: none">
                        Monroeside
                        </option>
                        <option value="17" data-parent="4" style="display: none">
                        Muellerville
                        </option>
                        <option value="12" data-parent="3" style="display: none">
                        New Bennieville
                        </option>
                        <option value="1" data-parent="1" style="display: none">
                        New Britneystad
                        </option>
                        <option value="21" data-parent="5" style="display: none">
                        New Devenmouth
                        </option>
                        <option value="22" data-parent="5" style="display: none">
                        North Alvah
                        </option>
                        <option value="20" data-parent="4" style="display: none">
                        Port Johnson
                        </option>
                        <option value="19" data-parent="4" style="display: none">
                        South Shanellefort
                        </option>
                        <option value="11" data-parent="3" style="display: none">
                        Toyport
                        </option>
                        <option value="4" data-parent="1" style="display: none">
                        West Lulu
                        </option>
                    </select>
                </div>
                <div>
                    <input type="number" placeholder="Age (Days)" name="age" />
                </div>
                <div>
                    <select id="distanceSelect" name="distance_id">
                        <option value="">Radius (km)</option>
                        <option value="1">10</option>
                        <option value="2">25</option>
                        <option value="3">50</option>
                        <option value="4">100</option>
                        <option value="4">250</option>
                        <option value="4">N/A</option>
                    </select>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-find-a-listing-reset">
                Reset
                </button>
                <button class="btn btn-primary btn-find-a-listing-submit">
                Search
                </button>
            </div>
        </form>
    </div>
</section>
<!--/ Find a listing form -->
