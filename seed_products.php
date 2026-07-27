<?php
// Connection details come from the environment so no credentials live in the
// repo. Defaults target the local XAMPP database.
$conn = mysqli_init();
$conn->real_connect(
    getenv('DB_HOST') ?: 'localhost',
    getenv('DB_USER') ?: 'root',
    getenv('DB_PASS') ?: '',
    getenv('DB_NAME') ?: 'b2btradeservices',
    (int) (getenv('DB_PORT') ?: 3306),
);
if ($conn->connect_error) { die('Connection failed: ' . $conn->connect_error); }
echo "Connected.\n";

$supplierIds = [9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28];

$products = [
    [1,0,'Premium Reinforced Steel Bars','premium-reinforced-steel-bars','High-quality reinforced steel bars for construction projects. Available in various diameters from 8mm to 32mm. Meets international ASTM A615 standards.','Diameter: 8-32mm|Grade: Grade 60|Length: 6m, 9m, 12m','product_build_1_1.jpg',500,'Tons','$450 - $550 / Ton','5000 Tons/Month','15-20 days','Bundled with steel straps','Jebel Ali Port','T/T, L/C','ISO 9001, ASTM Certified',1],
    [1,5,'Italian White Marble Slabs','italian-white-marble-slabs','Premium quality Italian white marble slabs. Perfect for luxury interior and exterior applications including flooring, wall cladding, and countertops.','Thickness: 15-30mm|Size: 240x120cm|Finish: Polished, Honed','product_build_1_2.jpg',50,'Slabs','$80 - $150 / sqm','2000 sqm/Month','20-30 days','Wooden crates with foam','Mersin Port','T/T, L/C','CE, ISO 9001',1],
    [1,15,'Portland Cement OPC 42.5N','portland-cement-opc','Ordinary Portland Cement Grade 42.5N for all types of construction. Consistent quality with high early strength.','Grade: 42.5N|Type: OPC|Setting: Initial 45min','product_build_1_3.jpg',1000,'Bags','$4.50 - $5.50 / Bag','50000 Bags/Month','7-14 days','50kg woven bags on pallets','Alexandria Port','T/T, L/C','EN 197-1, ISO 9001',0],
    [1,15,'Porcelain Floor Tiles 60x60cm','porcelain-floor-tiles-60x60','High-quality glazed porcelain floor tiles with wood-grain finish. Scratch-resistant and easy to maintain.','Size: 60x60cm|Thickness: 9.5mm|Water Absorption: <0.5%','product_build_1_4.jpg',200,'sqm','$8 - $15 / sqm','10000 sqm/Month','10-15 days','Carton boxes on pallets','Alexandria Port','T/T, L/C','ISO 13006, CE',0],
    [1,0,'Galvanized Steel Pipes','galvanized-steel-pipes','Hot-dip galvanized steel pipes for plumbing, scaffolding, and structural. Zinc-coated for corrosion resistance.','Diameter: 1/2" to 8"|Wall: 1.5-6mm|Length: 6m','product_build_1_5.jpg',100,'Tons','$600 - $800 / Ton','3000 Tons/Month','10-15 days','Bundled with plastic caps','Jebel Ali Port','T/T, L/C','ASTM A53, ISO 9001',0],
    [1,5,'Granite Kitchen Countertops','granite-kitchen-countertops','Premium natural granite countertops. Custom cut with polished edges.','Thickness: 20mm, 30mm|Colors: Black Galaxy, Kashmir White','product_build_1_6.jpg',20,'Pieces','$50 - $120 / sqm','500 sqm/Month','15-25 days','Wooden crate with foam','Mersin Port','T/T, L/C','CE Certified',0],
    [1,19,'Aluminum Window Profiles','aluminum-window-profiles','Extruded aluminum profiles for windows and doors. Thermal break technology.','Alloy: 6063-T5|Wall: 1.4mm|System: Sliding, Casement','product_build_1_7.jpg',500,'Meters','$3 - $8 / Meter','20000 Meters/Month','15-20 days','Protective film, shrink wrap','Khalifa Bin Salman Port','T/T, L/C','ISO 9001, QUALICOAT',0],
    [1,19,'Stainless Steel Railings','stainless-steel-railings','Modern SS304 railing systems for balconies and staircases. Mirror or satin finish.','Grade: SS 304|Pipe: 50mm|Height: 900-1100mm','product_build_1_8.jpg',50,'Meters','$35 - $65 / Meter','2000 Meters/Month','10-15 days','Bubble wrap in cartons','Khalifa Bin Salman Port','T/T','ISO 9001',0],

    [2,7,'Premium Jasmine Rice 5% Broken','premium-jasmine-rice-5-broken','Vietnamese premium long-grain jasmine rice with distinctive aroma. Carefully milled and sorted.','Broken: 5% Max|Moisture: 14% Max|Length: 6.8mm+','product_agri_1_1.jpg',25,'Tons','$480 - $550 / Ton','5000 Tons/Month','14-21 days','25kg, 50kg PP bags','Cat Lai Port','T/T, L/C','ISO 22000, HACCP',1],
    [2,7,'Robusta Coffee Beans Grade 1','robusta-coffee-beans-grade1','High-quality Vietnamese Robusta coffee beans, screen 18. Strong flavor for espresso and instant coffee.','Screen: 18|Moisture: 12.5% Max|Polished: Yes','product_agri_1_2.jpg',20,'Tons','$2200 - $2800 / Ton','500 Tons/Month','14-21 days','60kg jute bags or bulk','Cat Lai Port','T/T, L/C','ISO 22000, UTZ, Fair Trade',1],
    [2,7,'Raw Cashew Nuts W320','raw-cashew-nuts-w320','Premium whole cashew nuts grade W320. Clean white color. Perfect for snacking and confectionery.','Grade: W320|Moisture: 5% Max|Color: White/Ivory','product_agri_1_3.jpg',5,'Tons','$7500 - $9000 / Ton','200 Tons/Month','14-21 days','Tin cans in cartons','Cat Lai Port','T/T, L/C','HACCP, BRC, FDA',0],
    [2,7,'Black Pepper Whole FAQ','black-pepper-whole-faq','Vietnamese black pepper. Sun-dried whole peppercorns for grinding and food processing.','Moisture: 13% Max|Density: 550 g/L Min','product_agri_1_4.jpg',10,'Tons','$3800 - $4500 / Ton','300 Tons/Month','10-14 days','25kg PP bags','Cat Lai Port','T/T, L/C','ASTA, ISO 22000',0],
    [2,7,'Dried Turmeric Finger Whole','dried-turmeric-finger-whole','Premium dried turmeric fingers with high curcumin content (3-5%). Organically grown.','Curcumin: 3-5%|Moisture: 10% Max|Length: 5-7cm','product_agri_1_5.jpg',5,'Tons','$1800 - $2500 / Ton','200 Tons/Month','10-14 days','25kg PP bags','Cat Lai Port','T/T, L/C','USDA Organic, HACCP',0],
    [2,7,'Coconut Desiccated Fine Grade','coconut-desiccated-fine','Premium desiccated coconut. Pure white with natural aroma. Ideal for confectionery and baking.','Fat: 60-65%|Moisture: 2.5% Max|Color: White','product_agri_1_6.jpg',10,'Tons','$1400 - $1800 / Ton','500 Tons/Month','14-21 days','25kg paper bags','Cat Lai Port','T/T, L/C','HACCP, BRC, Kosher',0],
    [2,7,'Organic Green Tea Leaves','organic-green-tea-leaves','Premium organic green tea from highland plantations. Maximum antioxidant content.','Type: Green Tea|Moisture: 6% Max','product_agri_1_7.jpg',5,'Tons','$3500 - $6000 / Ton','100 Tons/Month','10-14 days','Paper bags with foil lining','Cat Lai Port','T/T, L/C','USDA Organic, ISO 22000',0],

    [3,1,'100% Cotton Combed Yarn 30s','cotton-combed-yarn-30s','Premium quality 100% cotton combed yarn for knitting and weaving. Excellent uniformity.','Count: 30s|Composition: 100% Cotton|Type: Combed Ring Spun','product_textile_1_1.jpg',10,'Tons','$2.80 - $3.20 / KG','200 Tons/Month','21-30 days','Cone packing in cartons','Karachi Port','T/T, L/C','OEKO-TEX, ISO 9001',1],
    [3,1,'Cotton Terry Bath Towels','cotton-terry-bath-towels','Luxurious 100% cotton terry bath towels. Highly absorbent. 48 color options.','Weight: 500-700 GSM|Size: 70x140cm|Colors: 48','product_textile_1_2.jpg',3000,'Pieces','$3.50 - $6.00 / Piece','50000 Pieces/Month','30-45 days','Poly bag, master carton','Karachi Port','T/T, L/C','OEKO-TEX, GOTS',0],
    [3,4,'Mens Slim Fit Denim Jeans','mens-slim-fit-denim-jeans','High-quality men\'s slim fit denim. Premium stretch denim with modern washes.','Fabric: 98% Cotton 2% Spandex|Weight: 10-12 oz|Sizes: 28-42','product_textile_1_3.jpg',1000,'Pieces','$8 - $14 / Piece','30000 Pieces/Month','45-60 days','Folded poly bags, 20pcs/carton','Chittagong Port','T/T, L/C','BSCI, SEDEX, OEKO-TEX',1],
    [3,4,'Ladies Cotton T-Shirts','ladies-cotton-t-shirts','Premium ladies cotton t-shirts. Reactive dyed colors. Custom printing available.','Fabric: 100% Cotton|Weight: 160-180 GSM|Sizes: XS-XXL','product_textile_1_4.jpg',2000,'Pieces','$2.50 - $5.00 / Piece','50000 Pieces/Month','30-45 days','Poly bag, 24pcs/carton','Chittagong Port','T/T, L/C','BSCI, WRAP, OEKO-TEX',0],
    [3,1,'Egyptian Cotton Bed Sheet Set','egyptian-cotton-bed-sheet-set','Luxurious 400TC Egyptian cotton bed sheet set. Includes flat sheet, fitted sheet, 2 pillowcases.','Thread Count: 400TC|Material: Egyptian Cotton|Colors: 24','product_textile_1_5.jpg',500,'Sets','$18 - $35 / Set','10000 Sets/Month','30-45 days','PVC bag with insert card','Karachi Port','T/T, L/C','OEKO-TEX, GOTS',0],
    [3,4,'Woven Plaid Flannel Shirts','woven-plaid-flannel-shirts','Men\'s premium brushed flannel plaid shirts. Double-brushed for softness.','Fabric: Cotton Flannel|Weight: 180-200 GSM|Sizes: S-3XL','product_textile_1_6.jpg',1500,'Pieces','$6 - $10 / Piece','20000 Pieces/Month','45-60 days','Folded in poly bags','Chittagong Port','T/T, L/C','SEDEX, OEKO-TEX',0],
    [3,1,'Organic Cotton Canvas Fabric','organic-cotton-canvas','Heavy-weight organic cotton canvas for bags, upholstery, workwear. GOTS certified.','Weight: 10-16 oz/yd|Width: 58-60 inches|100% Organic','product_textile_1_7.jpg',2000,'Meters','$3.50 - $6.00 / Meter','50000 Meters/Month','21-30 days','Rolled on tubes','Karachi Port','T/T, L/C','GOTS, OEKO-TEX',0],
    [3,4,'Sports Performance Activewear','sports-activewear','Moisture-wicking activewear from recycled polyester. 4-way stretch. Sublimation printing.','Fabric: Recycled Polyester/Spandex|Weight: 180-220 GSM','product_textile_1_8.jpg',500,'Pieces','$5 - $12 / Piece','20000 Pieces/Month','30-45 days','Individual poly bag','Chittagong Port','T/T, L/C','GRS, OEKO-TEX, BSCI',0],

    [4,3,'Indoor P2.5 LED Display Panel','indoor-p25-led-display','Ultra-high resolution indoor LED display. Perfect for shopping malls and advertising.','Pixel Pitch: 2.5mm|Brightness: 800-1200 nits|Refresh: 3840Hz','product_elec_1_1.jpg',10,'sqm','$350 - $550 / sqm','5000 sqm/Month','7-14 days','Flight case','Shenzhen Port','T/T, L/C, PayPal','CE, FCC, RoHS, UL',1],
    [4,3,'Smart WiFi Security Camera 4K','smart-wifi-camera-4k','Advanced 4K WiFi security camera with AI detection, night vision, two-way audio. IP66.','Resolution: 4K 8MP|Night Vision: 30m|Storage: Cloud + 256GB SD','product_elec_1_2.jpg',500,'Pieces','$28 - $45 / Piece','50000 Pieces/Month','7-10 days','Color box, 20pcs/master','Shenzhen Port','T/T, PayPal','CE, FCC, RoHS',1],
    [4,11,'FR4 PCB Prototype Board','fr4-pcb-prototype','Custom FR4 PCB manufacturing. 1 to 16 layers. Quick turnaround. SMT assembly available.','Layers: 1-16|Material: FR4|Min Trace: 3mil','product_elec_1_3.jpg',5,'sqm','$1 - $15 / sqm','10000 sqm/Month','3-7 days','Vacuum sealed anti-static','Port Klang','T/T, PayPal','ISO 9001, UL, IPC-A-600',0],
    [4,3,'USB-C Fast Charging Cable 100W','usb-c-cable-100w','Premium USB-C fast charging cable. 100W PD. Nylon braided. 1m and 2m lengths.','Power: 100W PD|Data: USB 3.1 10Gbps|Material: Nylon','product_elec_1_4.jpg',1000,'Pieces','$1.20 - $2.50 / Piece','200000 Pieces/Month','5-7 days','Individual box, 100pcs/master','Shenzhen Port','T/T, PayPal','MFi, USB-IF, CE, RoHS',0],
    [4,11,'Industrial Temperature Sensor','industrial-temp-sensor','PT100 RTD temperature sensor. SS316 probe. Accuracy ±0.1°C.','Type: PT100|Range: -200 to 600°C|Accuracy: ±0.1°C','product_elec_1_5.jpg',100,'Pieces','$5 - $25 / Piece','50000 Pieces/Month','7-10 days','Individual box, foam','Port Klang','T/T, L/C','CE, ISO 9001',0],
    [4,3,'Bluetooth 5.3 TWS Earbuds','bluetooth-53-tws-earbuds','True wireless earbuds with ANC. Hi-Fi audio. 30 hours playtime. IPX5.','Bluetooth: 5.3|ANC: -35dB|Playtime: 30h|IPX5','product_elec_1_6.jpg',500,'Pieces','$8 - $18 / Piece','100000 Pieces/Month','5-10 days','Color gift box','Shenzhen Port','T/T, PayPal','CE, FCC, BQB',0],
    [4,11,'Waterproof Connector M12','waterproof-connector-m12','Industrial M12 waterproof connectors. IP67/IP68. 3 to 12 pin configurations.','Type: M12|Rating: IP67/IP68|Pins: 3-12','product_elec_1_7.jpg',500,'Pieces','$0.80 - $3.50 / Piece','500000 Pieces/Month','5-7 days','Tray packing, anti-static','Port Klang','T/T','UL, CE, IP67/IP68',0],
    [4,3,'Outdoor P4 LED Display Cabinet','outdoor-p4-led-display','Weatherproof outdoor LED display. IP65. 6500 nits brightness. Die-cast aluminum.','Pixel Pitch: 4mm|Brightness: 6500 nits|IP65','product_elec_1_8.jpg',5,'sqm','$450 - $700 / sqm','3000 sqm/Month','10-14 days','Flight case','Shenzhen Port','T/T, L/C','CE, FCC, RoHS, IP65',0],

    [5,8,'CNC Vertical Machining Center','cnc-vertical-machining','High-precision CNC VMC with Fanuc controller. 3-axis. For mold making and precision parts.','Travel: X800 Y500 Z500mm|Spindle: 12000 RPM|Controller: Fanuc','product_mach_1_1.jpg',1,'Unit','$35,000 - $65,000','20 Units/Month','45-60 days','Wooden crate, sea-worthy','Hamburg Port','T/T 30/70, L/C','CE, ISO 9001',1],
    [5,8,'Hydraulic Press Machine 200T','hydraulic-press-200t','200-ton hydraulic press for metal forming and stamping. PLC controlled.','Capacity: 200 Tons|Stroke: 300mm|Control: Siemens PLC','product_mach_1_2.jpg',1,'Unit','$18,000 - $28,000','10 Units/Month','30-45 days','Container loading','Hamburg Port','T/T 30/70, L/C','CE, ISO 9001',0],
    [5,8,'Plastic Injection Molding Machine','injection-molding-machine','High-speed injection molding with servo motor. Energy saving up to 60%.','Clamping: 100-500 Tons|Shot: 50-2000g|Motor: Servo','product_mach_1_3.jpg',1,'Unit','$25,000 - $85,000','15 Units/Month','30-45 days','Open-top container','Hamburg Port','T/T, L/C','CE, ISO 9001, EUROMAP',1],
    [5,14,'Digital Hardness Tester','digital-hardness-tester','Precision digital Rockwell hardness tester. Automatic test cycle. ASTM E18 compliant.','Scale: HRA, HRB, HRC|Load: 60-150 kgf|Accuracy: ±0.5 HR','product_mach_1_4.jpg',5,'Units','$2,500 - $5,000','50 Units/Month','14-21 days','Foam-lined wooden case','Kobe Port','T/T, L/C','ISO 6508, JIS B 7726',0],
    [5,14,'Optical Profile Projector','optical-profile-projector','High-precision optical projector for dimensional measurement. Digital readout.','Screen: 300mm|Magnification: 10x, 20x, 50x|Readout: 0.001mm','product_mach_1_5.jpg',3,'Units','$4,000 - $8,000','30 Units/Month','14-21 days','Protective crate','Kobe Port','T/T, L/C','JIS B 7184, ISO 9001',0],
    [5,8,'Automatic Packaging Machine','auto-packaging-machine','High-speed VFFS packaging machine. Up to 80 bags/min. Touch screen PLC.','Speed: 80 bags/min|Bag: W60-320mm|Film: PE, PP, PET','product_mach_1_6.jpg',1,'Unit','$12,000 - $35,000','20 Units/Month','20-30 days','Wooden case','Hamburg Port','T/T, L/C','CE, ISO 9001',0],
    [5,8,'Laser Cutting Machine 3kW','laser-cutting-3kw','3kW fiber laser cutter for metal sheets. IPG source. Up to 40m/min.','Power: 3000W|Area: 3000x1500mm|Source: IPG Fiber','product_mach_1_7.jpg',1,'Unit','$28,000 - $55,000','10 Units/Month','30-45 days','Wooden crate','Hamburg Port','T/T 30/70, L/C','CE, ISO 9001',0],
    [5,14,'Coordinate Measuring Machine','coordinate-measuring-machine','Bridge-type CMM with Renishaw probe. PC-DMIS software included.','Range: 700x1000x600mm|Accuracy: 2.5+L/300 μm','product_mach_1_8.jpg',1,'Unit','$45,000 - $90,000','5 Units/Month','30-45 days','Climate-controlled crate','Kobe Port','T/T, L/C','ISO 10360, JIS B 7440',0],

    [6,2,'Industrial Sodium Hydroxide','industrial-sodium-hydroxide','High-purity caustic soda flakes/pearls. For soap, water treatment, paper.','Purity: 99% Min|Form: Flakes, Pearls|Fe: 15ppm Max','product_chem_1_1.jpg',20,'Tons','$350 - $450 / Ton','2000 Tons/Month','10-15 days','25kg bags on pallets','Nhava Sheva Port','T/T, L/C','ISO 9001, REACH',0],
    [6,6,'HDPE Granules Virgin Grade','hdpe-granules-virgin','Virgin HDPE granules for blow molding, injection, film extrusion.','MFI: 0.3-30 g/10min|Density: 0.941-0.965','product_chem_1_2.jpg',25,'Tons','$1,100 - $1,400 / Ton','5000 Tons/Month','14-21 days','25kg bags on pallets','Jubail Port','T/T, L/C','ISO 9001, FDA',1],
    [6,2,'Epoxy Resin System','epoxy-resin-system','Two-component epoxy for coatings, adhesives, composites. Excellent properties.','Viscosity: 500-1500 cPs|Shore: 80-85D','product_chem_1_3.jpg',1,'Tons','$2,200 - $3,500 / Ton','500 Tons/Month','10-15 days','200L drums or IBC','Nhava Sheva Port','T/T, L/C','ISO 9001, REACH',0],
    [6,6,'PVC Resin Suspension Grade SG5','pvc-resin-sg5','Premium PVC resin for pipe, profile, and sheet manufacturing.','K Value: 66-68|Density: 0.45-0.55|Whiteness: 80+','product_chem_1_4.jpg',25,'Tons','$750 - $900 / Ton','3000 Tons/Month','14-21 days','25kg bags, 1 ton bags','Jubail Port','T/T, L/C','ISO 9001, SGS',0],
    [6,2,'Industrial Solvent IPA 99.5%','industrial-solvent-ipa','High-purity isopropyl alcohol for pharmaceutical and electronic cleaning.','Purity: 99.5% Min|Water: 0.2% Max','product_chem_1_5.jpg',20,'Tons','$800 - $1,100 / Ton','1000 Tons/Month','7-14 days','160kg drums, IBC, bulk','Nhava Sheva Port','T/T, L/C','ISO 9001, REACH, GMP',0],
    [6,6,'Industrial Lubricant Base Oil','industrial-base-oil','Group II/III base oils for automotive and industrial lubricant manufacturing.','Group: II/III|Viscosity: 4-12 cSt|Sulfur: <10ppm','product_chem_1_6.jpg',100,'Tons','$800 - $1,200 / Ton','10000 Tons/Month','14-21 days','Flexitank, ISO tank, drums','Ras Tanura Port','T/T, L/C','ISO 9001, API',0],
    [6,2,'Polyurethane Foam Spray','polyurethane-foam-spray','Two-component spray PU foam for insulation. R-value 6.5/inch.','Type: Closed/Open Cell|R-Value: 6.5/inch|Density: 28-32 kg/m³','product_chem_1_7.jpg',5,'Tons','$2,800 - $4,000 / Ton','200 Tons/Month','10-15 days','200L drums, A+B sets','Nhava Sheva Port','T/T, L/C','ISO 9001, UL',0],
    [6,6,'Titanium Dioxide Rutile Grade','titanium-dioxide-rutile','TiO2 rutile grade for paint, coating, plastic. Excellent whiteness.','TiO2: 94% Min|Type: Rutile|Whiteness: 97+','product_chem_1_8.jpg',10,'Tons','$2,200 - $2,800 / Ton','1000 Tons/Month','14-21 days','25kg bags on pallets','Jubail Port','T/T, L/C','ISO 9001, REACH, FDA',0],

    [7,0,'Extruded Aluminum Profiles T6','extruded-aluminum-t6','Custom extruded aluminum profiles 6000 series. Architectural and industrial.','Alloy: 6061/6063|Temper: T5/T6|Length: Up to 7m','product_metal_1_1.jpg',2,'Tons','$2,500 - $3,200 / Ton','500 Tons/Month','15-20 days','Stretch wrap on pallets','Jebel Ali Port','T/T, L/C','ISO 9001, ASTM B221',1],
    [7,0,'Hot Rolled Steel Coils HRC','hot-rolled-steel-coils','Premium HRC for automotive, shipbuilding, pipeline applications.','Thickness: 1.2-25mm|Width: 1000-2000mm|Grade: Q235, SS400','product_metal_1_2.jpg',50,'Tons','$500 - $700 / Ton','10000 Tons/Month','15-25 days','Eye-to-sky, strapped','Jebel Ali Port','T/T, L/C','ISO 9001, Mill Certificate',0],
    [7,19,'Copper Cathode 99.99% LME','copper-cathode-9999','LME registered copper cathodes 99.99%. For wire rod and cable.','Purity: 99.99%|Weight: 80-125kg/sheet|Size: 920x820mm','product_metal_1_3.jpg',25,'Tons','LME + premium','500 Tons/Month','7-14 days','Bundled on wooden pallets','Khalifa Bin Salman Port','T/T, L/C','LME Registered, ISO 9001',0],
    [7,0,'Stainless Steel Sheet 304','stainless-steel-sheet-304','Cold rolled SS304 sheets. 2B, BA, and mirror finishes.','Grade: 304|Thickness: 0.5-6mm|Finish: 2B, BA, Mirror','product_metal_1_4.jpg',5,'Tons','$2,000 - $2,800 / Ton','3000 Tons/Month','7-14 days','Protective film, wooden pallet','Jebel Ali Port','T/T, L/C','Mill Certificate, ISO 9001',0],
    [7,0,'Seamless Steel Tubes ASTM A106','seamless-steel-tubes-a106','Seamless carbon steel tubes for high-temperature service. Refineries and boilers.','Grade: A106 Gr.B|OD: 21.3-508mm|Wall: 2.77-50mm','product_metal_1_5.jpg',25,'Tons','$700 - $1,100 / Ton','3000 Tons/Month','14-21 days','Bundled, end caps','Jebel Ali Port','T/T, L/C','API 5L, ASTM A106',0],
    [7,19,'Wrought Iron Gates Custom','wrought-iron-gates-custom','Custom wrought iron gates. Hand-forged scrollwork with powder coat.','Material: Mild Steel|Height: Up to 4m|Finish: Galvanized + Powder Coat','product_metal_1_6.jpg',5,'Pieces','$500 - $3,000 / Piece','100 Pieces/Month','21-30 days','Crated for shipping','Khalifa Bin Salman Port','T/T','ISO 9001',0],
    [7,0,'Galvanized Steel Wire Rope','galvanized-wire-rope','Hot-dip galvanized wire ropes for lifting, marine, construction.','Construction: 6x19, 6x36|Diameter: 6-52mm|Grade: 1770 MPa','product_metal_1_7.jpg',10,'Tons','$1,200 - $1,800 / Ton','500 Tons/Month','10-14 days','Wooden reels','Jebel Ali Port','T/T, L/C','API 9A, ISO 2408',0],

    [8,13,'Ceramic Disc Brake Pads Set','ceramic-brake-pads','Premium ceramic brake pads. Low dust, low noise. OE replacement.','Type: Ceramic|Temp: Up to 400°C|Warranty: 50,000 km','product_auto_1_1.jpg',500,'Sets','$5 - $15 / Set','50000 Sets/Month','14-21 days','Color box, 20 sets/master','Felixstowe Port','T/T, L/C','ECE R90, ISO 9001',1],
    [8,13,'AGM Start-Stop Car Battery','agm-car-battery','Advanced AGM battery for start-stop vehicles. 3-year warranty.','Capacity: 60-100Ah|CCA: 600-900A|Type: AGM','product_auto_1_2.jpg',200,'Pieces','$65 - $120 / Piece','10000 Pieces/Month','14-21 days','Individual box on pallet','Felixstowe Port','T/T, L/C','IEC 60095, TUV',0],
    [8,13,'Suspension Coil Springs','suspension-coil-springs','OE quality suspension coil springs. Shot-peened epoxy-coated.','Material: Chrome Vanadium|Applications: 5000+','product_auto_1_3.jpg',300,'Pieces','$8 - $25 / Piece','30000 Pieces/Month','14-21 days','Shrink wrapped in carton','Felixstowe Port','T/T, L/C','ISO/TS 16949, IATF',0],
    [8,13,'LED Headlight Bulb Kit H7','led-headlight-h7','300% brighter LED headlight kit. 50,000 hour lifespan. Plug-and-play.','Lumens: 12000LM/pair|Color: 6000K|Lifespan: 50,000h','product_auto_1_4.jpg',500,'Pairs','$12 - $28 / Pair','50000 Pairs/Month','7-14 days','Color retail box','Felixstowe Port','T/T, PayPal','CE, E-Mark, RoHS',0],
    [8,13,'Windshield Wiper Blades','windshield-wiper-blades','Universal flat wiper blades with Teflon coating. 14" to 28".','Type: Flat|Material: Rubber + Teflon|Sizes: 14-28 inches','product_auto_1_5.jpg',500,'Pieces','$1.50 - $4.00 / Piece','100000 Pieces/Month','7-14 days','Blister pack, 50pcs/master','Felixstowe Port','T/T','ECE R43, ISO 9001',0],

    [9,9,'Disposable Nitrile Exam Gloves','nitrile-exam-gloves','Medical-grade powder-free nitrile gloves. AQL 1.5. Textured fingertips.','Material: Nitrile|AQL: 1.5|Thickness: 3.5-5 mil','product_med_1_1.jpg',100,'Cases','$38 - $55 / Case','5000 Cases/Month','14-21 days','100pcs/box, 10 boxes/case','Chicago Port','T/T, L/C','FDA 510(k), EN 455',1],
    [9,9,'Digital Blood Pressure Monitor','digital-bp-monitor','Clinically validated automatic upper arm BP monitor. Memory for 2 users.','Method: Oscillometric|Accuracy: ±3mmHg|Memory: 2x120','product_med_1_2.jpg',200,'Pieces','$18 - $35 / Piece','10000 Pieces/Month','10-14 days','Gift box with case','Chicago Port','T/T, L/C','FDA, CE, ISO 13485',0],
    [9,18,'Paracetamol Tablets BP 500mg','paracetamol-tablets-500mg','Pharmaceutical-grade paracetamol 500mg. WHO-GMP certified facility.','Strength: 500mg|Standard: BP/USP|Shelf Life: 36 months','product_med_1_3.jpg',10000,'Boxes','$0.50 - $1.20 / Box','500000 Boxes/Month','21-30 days','Blister in printed carton','Mundra Port','T/T, L/C','WHO-GMP, ISO 9001, FDA',0],
    [9,9,'Portable Pulse Oximeter','portable-pulse-oximeter','Fingertip pulse oximeter with OLED display. 10-second measurement.','SpO2: 70-100%|Accuracy: ±2%|PR: 30-250 BPM','product_med_1_4.jpg',500,'Pieces','$6 - $15 / Piece','50000 Pieces/Month','7-10 days','Individual box with lanyard','Chicago Port','T/T, PayPal','FDA, CE, ISO 13485',0],
    [9,18,'Ashwagandha Extract Capsules','ashwagandha-extract-capsules','Standardized Ashwagandha root extract (5% withanolides). Vegetarian capsules.','Strength: 500mg|Extract: 5% Withanolides|Count: 60 capsules','product_med_1_5.jpg',5000,'Bottles','$2.50 - $5.00 / Bottle','100000 Bottles/Month','14-21 days','HDPE bottle in carton','Mundra Port','T/T, L/C','GMP, ISO 22000, Halal',0],
    [9,9,'Infrared Forehead Thermometer','infrared-thermometer','Non-contact IR thermometer. 1-second reading. Color-coded fever alert.','Range: 32-42.9°C|Accuracy: ±0.2°C|Memory: 50','product_med_1_6.jpg',500,'Pieces','$5 - $12 / Piece','50000 Pieces/Month','5-10 days','Color box, 50pcs/master','Chicago Port','T/T, PayPal','FDA, CE, ISO 13485',0],
    [9,18,'Multivitamin Daily Formula','multivitamin-daily-formula','Comprehensive daily multivitamin with 23 essential nutrients. Film-coated.','Nutrients: 23|Form: Film-coated|Serving: 1 daily','product_med_1_7.jpg',5000,'Bottles','$1.50 - $4.00 / Bottle','200000 Bottles/Month','14-21 days','HDPE bottle in box','Mundra Port','T/T, L/C','GMP, ISO 22000, FDA',0],

    [10,10,'Modern Leather Sectional Sofa','leather-sectional-sofa','Premium genuine leather L-shaped sectional. High-density foam. Solid wood frame.','Material: Top Grain Leather|Frame: Solid Wood|Dimensions: 300x180cm','product_home_1_1.jpg',20,'Sets','$800 - $1,500 / Set','200 Sets/Month','30-45 days','Disassembled in cartons','Nansha Port','T/T 30/70, L/C','ISO 9001, BIFMA',1],
    [10,10,'Solid Wood Dining Table Set','solid-wood-dining-set','8-seater solid oak/walnut dining table with matching chairs.','Material: Oak/Walnut|Table: 200x90x75cm|Chairs: 8','product_home_1_2.jpg',30,'Sets','$450 - $900 / Set','150 Sets/Month','30-45 days','KD packed in cartons','Nansha Port','T/T, L/C','FSC, ISO 9001',0],
    [10,12,'Handwoven Wool Area Rug','handwoven-wool-rug','Traditional hand-knotted wool rugs with oriental patterns. NZ wool on cotton.','Pile: NZ Wool|Knots: 120-200/sq inch|Sizes: 4x6 to 12x15 ft','product_home_1_3.jpg',10,'Pieces','$200 - $2,000 / Piece','500 Pieces/Month','45-60 days','Rolled in poly wrap','Nhava Sheva Port','T/T, L/C','GoodWeave, ISO 9001',1],
    [10,12,'Brass Table Lamp Collection','brass-table-lamps','Handcrafted brass table lamps with jali work. Traditional Rajasthani designs.','Material: Solid Brass|Height: 30-60cm|Bulb: E27','product_home_1_4.jpg',50,'Pieces','$25 - $80 / Piece','2000 Pieces/Month','21-30 days','Individual box with foam','Nhava Sheva Port','T/T','CE (wiring)',0],
    [10,10,'Ergonomic Office Chair','ergonomic-office-chair','Professional mesh office chair. Adjustable lumbar, 4D armrests. 5-year warranty.','Max Load: 150kg|Armrest: 4D|Back: High-density Mesh','product_home_1_5.jpg',50,'Pieces','$80 - $200 / Piece','2000 Pieces/Month','14-21 days','KD in carton','Nansha Port','T/T, L/C','BIFMA, ISO 9001',0],
    [10,12,'Hand-Carved Marble Artifacts','hand-carved-marble-artifacts','White marble artifacts with semi-precious stone inlay. Mughal designs.','Material: White Makrana Marble|Inlay: Semi-precious Stones','product_home_1_6.jpg',20,'Pieces','$15 - $200 / Piece','1000 Pieces/Month','30-45 days','Foam box, double carton','Nhava Sheva Port','T/T','Handcraft Certificate',0],
    [10,10,'Executive Office Desk L-Shape','executive-office-desk','L-shaped executive desk with cable management. Scratch-resistant MFC MDF.','Material: MFC MDF|Dimensions: 180x160x75cm|Load: 100kg','product_home_1_7.jpg',30,'Pieces','$150 - $350 / Piece','500 Pieces/Month','14-21 days','Flat packed 2 cartons/desk','Nansha Port','T/T, L/C','ISO 9001, E1 Board',0],

    [11,16,'Commercial Treadmill Heavy Duty','commercial-treadmill','Professional 4.0HP AC treadmill with cushioned deck. Entertainment console.','Motor: 4.0HP AC|Speed: 0.5-20 km/h|Belt: 56x152cm','product_sport_1_1.jpg',10,'Units','$1,800 - $3,500','200 Units/Month','21-30 days','Assembled in wooden crate','Busan Port','T/T, L/C','CE, EN 957, ISO 20957',1],
    [11,16,'Adjustable Dumbbell Set','adjustable-dumbbell-set','Quick-change dial system. 5-52.5 lbs. Replaces 15 fixed sets.','Range: 5-52.5 lbs|Increments: 2.5 lbs|Material: Steel/Nylon','product_sport_1_2.jpg',100,'Sets','$150 - $280 / Set','2000 Sets/Month','14-21 days','Color box with foam','Busan Port','T/T','CE, ASTM F2216',0],
    [11,16,'Premium Natural Rubber Yoga Mat','natural-rubber-yoga-mat','Eco-friendly natural rubber with microfiber suede. Excellent grip. 12 designs.','Material: Natural Rubber + Suede|Thickness: 5mm|Size: 183x68cm','product_sport_1_3.jpg',500,'Pieces','$12 - $25 / Piece','10000 Pieces/Month','14-21 days','Carry strap, printed box','Busan Port','T/T, PayPal','SGS, OEKO-TEX',0],
    [11,16,'Resistance Bands Set 5 Levels','resistance-bands-set','Professional latex resistance bands. 5 levels. Includes door anchor, handles.','Levels: 5 (10-50 lbs)|Material: Natural Latex|Length: 120cm','product_sport_1_4.jpg',1000,'Sets','$4 - $10 / Set','50000 Sets/Month','7-14 days','Carry bag in color box','Busan Port','T/T, PayPal','SGS, CE',0],
    [11,16,'Multi-Station Home Gym','multi-station-home-gym','All-in-one home gym with 200 lb weight stack. 5+ exercise stations.','Stations: 5+|Weight Stack: 200 lbs|Frame: 11-gauge Steel','product_sport_1_5.jpg',10,'Units','$800 - $2,000','100 Units/Month','21-30 days','KD in reinforced cartons','Busan Port','T/T, L/C','CE, EN 957, ASTM',0],

    [12,17,'Custom Corrugated Shipping Boxes','corrugated-shipping-boxes','Custom-printed corrugated boxes. Full CMYK printing. E-commerce and industrial.','Material: B, C, BC flute|Printing: Up to 6 colors','product_pack_1_1.jpg',1000,'Pieces','$0.30 - $3.00 / Piece','500000 Pieces/Month','10-14 days','Flat packed on pallets','Santos Port','T/T','FSC, ISO 9001',1],
    [12,17,'Flexible Packaging Pouches','flexible-packaging-pouches','Stand-up pouches with zipper. Multi-layer laminated for food and snacks.','Material: PET/AL/PE|Closure: Zipper|Printing: Rotogravure 10-color','product_pack_1_2.jpg',5000,'Pieces','$0.05 - $0.50 / Piece','2000000 Pieces/Month','14-21 days','Rolls or flat in cartons','Santos Port','T/T, L/C','FDA, ISO 22000, BRC',0],
    [12,17,'Self-Adhesive Product Labels','self-adhesive-labels','Custom labels on rolls. Digital and flexo printing. Paper, PP, PET, vinyl.','Material: Paper, PP, PET, Vinyl|Printing: Digital, Flexo','product_pack_1_3.jpg',5000,'Pieces','$0.02 - $0.15 / Piece','5000000 Pieces/Month','7-10 days','Rolls in carton boxes','Santos Port','T/T','ISO 9001, FSC',0],
    [12,17,'Kraft Paper Shopping Bags','kraft-paper-shopping-bags','Eco-friendly kraft bags with twisted rope handles. Custom printing.','Material: Kraft 120-250gsm|Handle: Twisted Rope|Printing: Up to 4 colors','product_pack_1_4.jpg',3000,'Pieces','$0.15 - $0.80 / Piece','200000 Pieces/Month','10-14 days','Flat packed in bundles','Santos Port','T/T','FSC, ISO 14001',0],
    [12,17,'Biodegradable Food Containers','biodegradable-food-containers','Sugarcane bagasse containers. Microwave safe. Compostable in 90 days.','Material: Sugarcane Bagasse|Sizes: 500-1000ml|Compostable: 90 days','product_pack_1_5.jpg',5000,'Pieces','$0.06 - $0.20 / Piece','1000000 Pieces/Month','10-14 days','Stacked in carton boxes','Santos Port','T/T','FDA, BPI, EN 13432',0],
    [12,17,'Shrink Wrap Film Rolls','shrink-wrap-film-rolls','Cross-linked POF shrink wrap. Crystal clear. For food, cosmetics, retail.','Material: POF|Thickness: 12-25 micron|Width: 150-800mm','product_pack_1_6.jpg',100,'Rolls','$2.50 - $8.00 / Roll','50000 Rolls/Month','7-10 days','Carton boxes on pallets','Santos Port','T/T','FDA, EU 10/2011',0],

    [1,5,'Onyx Stone Decorative Panels','onyx-stone-panels','Translucent onyx panels for backlit walls. Honey, green, white varieties.','Thickness: 10-20mm|Size: 120x60cm|Types: Honey, Green, White','product_build_1_9.jpg',20,'sqm','$150 - $400 / sqm','500 sqm/Month','20-30 days','Wooden crate, foam','Mersin Port','T/T, L/C','CE Certified',0],
];

echo "Inserting " . count($products) . " products...\n";
$inserted = 0;
$errors = 0;

foreach ($products as $p) {
    $suppIdx = $p[1];
    if (!isset($supplierIds[$suppIdx])) {
        echo "SKIP: supplier idx {$suppIdx} not found\n";
        $errors++;
        continue;
    }
    $suppId = $supplierIds[$suppIdx];
    $catId = $p[0];
    $name = $p[2];
    $slug = $p[3];
    $desc = $p[4];
    $specs = $p[5];
    $img = $p[6];
    $moq = $p[7];
    $unit = $p[8];
    $price = $p[9];
    $supply = $p[10];
    $delivery = $p[11];
    $packaging = $p[12];
    $port = $p[13];
    $payment = $p[14];
    $certs = $p[15];
    $featured = $p[16];
    $now = date('Y-m-d H:i:s', strtotime('-' . rand(1, 60) . ' days'));
    $now2 = $now;

    $stmt = $conn->prepare("INSERT INTO products (supplier_id, category_id, name, slug, description, specifications, main_image, min_order_quantity, min_order_unit, price_range, supply_ability, delivery_time, packaging, port, payment_terms, certifications, is_featured, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, ?)");
    $stmt->bind_param('iisssssissssssssiss',
        $suppId, $catId, $name, $slug, $desc, $specs,
        $img, $moq, $unit, $price, $supply,
        $delivery, $packaging, $port, $payment, $certs,
        $featured, $now, $now2
    );

    if ($stmt->execute()) {
        $inserted++;
        if ($inserted % 20 == 0) echo "  Inserted {$inserted}...\n";
    } else {
        echo "  ERROR: {$name}: " . $stmt->error . "\n";
        $errors++;
    }
    $stmt->close();
}

echo "\nDone! Inserted {$inserted} products. Errors: {$errors}\n";
$conn->close();
