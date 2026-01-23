import json
import random

# Product templates for each category
electronics_products = [
    {"name": "Smartphone", "price_range": (200, 500), "icon": "📱"},
    {"name": "Tablet", "price_range": (150, 400), "icon": "📱"},
    {"name": "Laptop", "price_range": (400, 1200), "icon": "💻"},
    {"name": "Desktop Computer", "price_range": (500, 1500), "icon": "🖥️"},
    {"name": "Monitor", "price_range": (150, 600), "icon": "🖥️"},
    {"name": "Keyboard", "price_range": (30, 150), "icon": "⌨️"},
    {"name": "Mouse", "price_range": (20, 100), "icon": "🖱️"},
    {"name": "Headphones", "price_range": (40, 300), "icon": "🎧"},
    {"name": "Earbuds", "price_range": (30, 250), "icon": "🎧"},
    {"name": "Speaker", "price_range": (50, 400), "icon": "🔊"},
    {"name": "Smartwatch", "price_range": (100, 500), "icon": "⌚"},
    {"name": "Fitness Tracker", "price_range": (50, 200), "icon": "⌚"},
    {"name": "Camera", "price_range": (200, 1000), "icon": "📷"},
    {"name": "Webcam", "price_range": (40, 150), "icon": "📷"},
    {"name": "Microphone", "price_range": (50, 300), "icon": "🎤"},
    {"name": "Router", "price_range": (40, 200), "icon": "📡"},
    {"name": "Power Bank", "price_range": (25, 80), "icon": "🔋"},
    {"name": "Charger", "price_range": (15, 60), "icon": "🔌"},
    {"name": "USB Cable", "price_range": (10, 30), "icon": "🔌"},
    {"name": "Hard Drive", "price_range": (60, 250), "icon": "💾"},
]

fashion_products = [
    {"name": "T-Shirt", "price_range": (15, 50), "icon": "👕"},
    {"name": "Shirt", "price_range": (25, 80), "icon": "👔"},
    {"name": "Jeans", "price_range": (40, 120), "icon": "👖"},
    {"name": "Pants", "price_range": (35, 100), "icon": "👖"},
    {"name": "Shorts", "price_range": (20, 60), "icon": "🩳"},
    {"name": "Dress", "price_range": (40, 150), "icon": "👗"},
    {"name": "Skirt", "price_range": (25, 80), "icon": "👗"},
    {"name": "Jacket", "price_range": (60, 200), "icon": "🧥"},
    {"name": "Coat", "price_range": (80, 300), "icon": "🧥"},
    {"name": "Sweater", "price_range": (35, 100), "icon": "🧶"},
    {"name": "Hoodie", "price_range": (40, 90), "icon": "🧥"},
    {"name": "Sneakers", "price_range": (50, 150), "icon": "👟"},
    {"name": "Boots", "price_range": (70, 200), "icon": "👞"},
    {"name": "Sandals", "price_range": (25, 80), "icon": "👡"},
    {"name": "Heels", "price_range": (50, 180), "icon": "👠"},
    {"name": "Watch", "price_range": (40, 300), "icon": "⌚"},
    {"name": "Sunglasses", "price_range": (30, 150), "icon": "🕶️"},
    {"name": "Hat", "price_range": (20, 60), "icon": "🧢"},
    {"name": "Scarf", "price_range": (15, 50), "icon": "🧣"},
    {"name": "Handbag", "price_range": (50, 250), "icon": "👜"},
]

home_products = [
    {"name": "Sofa", "price_range": (400, 1200), "icon": "🛋️"},
    {"name": "Chair", "price_range": (80, 300), "icon": "🪑"},
    {"name": "Table", "price_range": (150, 600), "icon": "🪑"},
    {"name": "Bed", "price_range": (300, 1000), "icon": "🛏️"},
    {"name": "Mattress", "price_range": (200, 800), "icon": "🛏️"},
    {"name": "Pillow", "price_range": (20, 80), "icon": "🛏️"},
    {"name": "Blanket", "price_range": (30, 100), "icon": "🛏️"},
    {"name": "Curtains", "price_range": (40, 150), "icon": "🪟"},
    {"name": "Rug", "price_range": (50, 300), "icon": "🧺"},
    {"name": "Lamp", "price_range": (30, 150), "icon": "💡"},
    {"name": "Mirror", "price_range": (40, 200), "icon": "🪞"},
    {"name": "Clock", "price_range": (25, 100), "icon": "🕐"},
    {"name": "Vase", "price_range": (20, 80), "icon": "🏺"},
    {"name": "Picture Frame", "price_range": (15, 60), "icon": "🖼️"},
    {"name": "Bookshelf", "price_range": (100, 400), "icon": "📚"},
    {"name": "Cabinet", "price_range": (150, 600), "icon": "🗄️"},
    {"name": "Coffee Maker", "price_range": (50, 250), "icon": "☕"},
    {"name": "Blender", "price_range": (40, 150), "icon": "🍹"},
    {"name": "Toaster", "price_range": (30, 100), "icon": "🍞"},
    {"name": "Microwave", "price_range": (80, 300), "icon": "🍽️"},
]

sports_products = [
    {"name": "Running Shoes", "price_range": (60, 180), "icon": "👟"},
    {"name": "Yoga Mat", "price_range": (20, 80), "icon": "🧘"},
    {"name": "Dumbbells", "price_range": (30, 150), "icon": "🏋️"},
    {"name": "Resistance Bands", "price_range": (15, 50), "icon": "🏋️"},
    {"name": "Jump Rope", "price_range": (10, 30), "icon": "🪢"},
    {"name": "Gym Bag", "price_range": (25, 80), "icon": "🎒"},
    {"name": "Water Bottle", "price_range": (15, 40), "icon": "💧"},
    {"name": "Protein Shaker", "price_range": (10, 30), "icon": "🥤"},
    {"name": "Fitness Tracker", "price_range": (50, 200), "icon": "⌚"},
    {"name": "Bicycle", "price_range": (200, 800), "icon": "🚴"},
    {"name": "Helmet", "price_range": (40, 120), "icon": "🪖"},
    {"name": "Tennis Racket", "price_range": (50, 200), "icon": "🎾"},
    {"name": "Basketball", "price_range": (20, 60), "icon": "🏀"},
    {"name": "Football", "price_range": (25, 70), "icon": "🏈"},
    {"name": "Soccer Ball", "price_range": (20, 60), "icon": "⚽"},
    {"name": "Baseball Bat", "price_range": (40, 150), "icon": "⚾"},
    {"name": "Golf Clubs", "price_range": (150, 600), "icon": "⛳"},
    {"name": "Swimming Goggles", "price_range": (15, 50), "icon": "🏊"},
    {"name": "Yoga Blocks", "price_range": (15, 40), "icon": "🧘"},
    {"name": "Foam Roller", "price_range": (20, 60), "icon": "🏋️"},
]

books_products = [
    {"name": "Fiction Novel", "price_range": (10, 30), "icon": "📚"},
    {"name": "Mystery Thriller", "price_range": (12, 28), "icon": "📖"},
    {"name": "Romance Book", "price_range": (10, 25), "icon": "📕"},
    {"name": "Science Fiction", "price_range": (12, 30), "icon": "📗"},
    {"name": "Fantasy Epic", "price_range": (15, 35), "icon": "📘"},
    {"name": "Biography", "price_range": (15, 35), "icon": "📙"},
    {"name": "Self-Help Guide", "price_range": (12, 30), "icon": "📓"},
    {"name": "Business Book", "price_range": (18, 40), "icon": "📔"},
    {"name": "Cookbook", "price_range": (20, 45), "icon": "📒"},
    {"name": "Travel Guide", "price_range": (15, 35), "icon": "📰"},
    {"name": "History Book", "price_range": (18, 40), "icon": "📚"},
    {"name": "Poetry Collection", "price_range": (10, 25), "icon": "📖"},
    {"name": "Art Book", "price_range": (25, 60), "icon": "📕"},
    {"name": "Photography Book", "price_range": (30, 70), "icon": "📗"},
    {"name": "Programming Guide", "price_range": (35, 80), "icon": "📘"},
    {"name": "Marketing Book", "price_range": (20, 45), "icon": "📙"},
    {"name": "Psychology Book", "price_range": (18, 40), "icon": "📓"},
    {"name": "Philosophy Book", "price_range": (15, 35), "icon": "📔"},
    {"name": "Children's Book", "price_range": (8, 20), "icon": "📒"},
    {"name": "Comic Book", "price_range": (5, 15), "icon": "📰"},
]

adjectives = ["Premium", "Professional", "Advanced", "Ultra", "Pro", "Elite", "Deluxe", "Supreme", "Master", "Expert", "Classic", "Modern", "Luxury", "Essential", "Perfect"]
qualities = ["Quality", "Performance", "Edition", "Series", "Collection", "Model", "Version", "Grade", "Plus", "Max"]

brands = {
    1: "TechPro",
    2: "StyleMax",
    3: "HomeComfort",
    4: "SportFit",
    5: "ReadMore",
    6: "ElectroPlus",
    7: "FashionHub",
    8: "GadgetWorld",
    9: "UrbanStyle",
    10: "CozyHome",
    11: "ActiveLife",
    12: "BookNest"
}

countries = ["USA", "Germany", "Japan", "China", "India", "Taiwan", "South Korea"]

def generate_products():
    products = {}
    product_id = 1
    
    # Category 1: Electronics (100 products)
    for i in range(100):
        template = electronics_products[i % len(electronics_products)]
        adj = random.choice(adjectives)
        qual = random.choice(qualities)
        name = f"{adj} {template['name']} {qual}"
        
        base_price = random.uniform(template['price_range'][0], template['price_range'][1])
        discount = random.choice([0, 0, 0, 5, 10, 15, 20, 25, 30])
        price = round(base_price * (1 - discount / 100), 2)
        
        products[str(product_id)] = {
            "id": product_id,
            "name": name,
            "slug": name.lower().replace(' ', '-') + f"-{product_id}",
            "category_id": 1,
            "brand_id": random.choice([1, 6, 8]),
            "price": price,
            "original_price": round(base_price, 2),
            "discount_percent": discount,
            "rating": round(random.uniform(3.5, 5.0), 1),
            "reviews_count": random.randint(10, 500),
            "stock": random.randint(0, 100),
            "description": f"High-quality {template['name'].lower()} with excellent features and performance",
            "long_description": f"Experience the best in class {template['name'].lower()} with advanced technology and superior craftsmanship. Perfect for your needs.",
            "features": [
                "High quality materials",
                "Advanced technology",
                "Durable construction",
                "Excellent performance",
                "Great value for money"
            ],
            "specifications": {
                "Model": f"EL-{product_id:04d}",
                "Brand": brands[random.choice([1, 6, 8])],
                "Warranty": f"{random.choice([1, 2, 3])} Year(s)",
                "Made In": random.choice(countries)
            },
            "variants": {
                "color": random.sample(["Black", "White", "Blue", "Red", "Gray", "Silver"], k=3),
                "size": []
            },
            "icon": template['icon'],
            "new": random.random() > 0.7,
            "featured": random.random() > 0.85
        }
        product_id += 1
    
    # Category 2: Fashion (100 products)
    for i in range(100):
        template = fashion_products[i % len(fashion_products)]
        adj = random.choice(adjectives)
        qual = random.choice(qualities)
        name = f"{adj} {template['name']} {qual}"
        
        base_price = random.uniform(template['price_range'][0], template['price_range'][1])
        discount = random.choice([0, 0, 0, 5, 10, 15, 20, 25, 30, 35])
        price = round(base_price * (1 - discount / 100), 2)
        
        products[str(product_id)] = {
            "id": product_id,
            "name": name,
            "slug": name.lower().replace(' ', '-') + f"-{product_id}",
            "category_id": 2,
            "brand_id": random.choice([2, 7, 9]),
            "price": price,
            "original_price": round(base_price, 2),
            "discount_percent": discount,
            "rating": round(random.uniform(3.5, 5.0), 1),
            "reviews_count": random.randint(10, 500),
            "stock": random.randint(0, 100),
            "description": f"Stylish {template['name'].lower()} perfect for any occasion",
            "long_description": f"Elevate your style with this premium {template['name'].lower()}. Crafted with attention to detail and quality materials.",
            "features": [
                "Premium fabric",
                "Comfortable fit",
                "Stylish design",
                "Durable quality",
                "Easy care"
            ],
            "specifications": {
                "Model": f"FA-{product_id:04d}",
                "Brand": brands[random.choice([2, 7, 9])],
                "Warranty": f"{random.choice([1, 2])} Year(s)",
                "Made In": random.choice(countries)
            },
            "variants": {
                "color": random.sample(["Black", "White", "Blue", "Red", "Gray", "Green", "Pink", "Navy"], k=4),
                "size": ["S", "M", "L", "XL"]
            },
            "icon": template['icon'],
            "new": random.random() > 0.7,
            "featured": random.random() > 0.85
        }
        product_id += 1
    
    # Category 3: Home & Living (100 products)
    for i in range(100):
        template = home_products[i % len(home_products)]
        adj = random.choice(adjectives)
        qual = random.choice(qualities)
        name = f"{adj} {template['name']} {qual}"
        
        base_price = random.uniform(template['price_range'][0], template['price_range'][1])
        discount = random.choice([0, 0, 0, 5, 10, 15, 20, 25])
        price = round(base_price * (1 - discount / 100), 2)
        
        products[str(product_id)] = {
            "id": product_id,
            "name": name,
            "slug": name.lower().replace(' ', '-') + f"-{product_id}",
            "category_id": 3,
            "brand_id": random.choice([3, 10]),
            "price": price,
            "original_price": round(base_price, 2),
            "discount_percent": discount,
            "rating": round(random.uniform(3.5, 5.0), 1),
            "reviews_count": random.randint(10, 500),
            "stock": random.randint(0, 100),
            "description": f"Quality {template['name'].lower()} for your home",
            "long_description": f"Transform your living space with this premium {template['name'].lower()}. Designed for comfort and style.",
            "features": [
                "Premium materials",
                "Modern design",
                "Durable construction",
                "Easy assembly",
                "Perfect fit"
            ],
            "specifications": {
                "Model": f"HM-{product_id:04d}",
                "Brand": brands[random.choice([3, 10])],
                "Warranty": f"{random.choice([1, 2, 3])} Year(s)",
                "Made In": random.choice(countries)
            },
            "variants": {
                "color": random.sample(["Black", "White", "Brown", "Gray", "Beige", "Oak"], k=3),
                "size": []
            },
            "icon": template['icon'],
            "new": random.random() > 0.7,
            "featured": random.random() > 0.85
        }
        product_id += 1
    
    # Category 4: Sports (100 products)
    for i in range(100):
        template = sports_products[i % len(sports_products)]
        adj = random.choice(adjectives)
        qual = random.choice(qualities)
        name = f"{adj} {template['name']} {qual}"
        
        base_price = random.uniform(template['price_range'][0], template['price_range'][1])
        discount = random.choice([0, 0, 0, 5, 10, 15, 20, 25])
        price = round(base_price * (1 - discount / 100), 2)
        
        products[str(product_id)] = {
            "id": product_id,
            "name": name,
            "slug": name.lower().replace(' ', '-') + f"-{product_id}",
            "category_id": 4,
            "brand_id": random.choice([4, 11]),
            "price": price,
            "original_price": round(base_price, 2),
            "discount_percent": discount,
            "rating": round(random.uniform(3.5, 5.0), 1),
            "reviews_count": random.randint(10, 500),
            "stock": random.randint(0, 100),
            "description": f"Professional {template['name'].lower()} for athletes",
            "long_description": f"Achieve your fitness goals with this high-performance {template['name'].lower()}. Built for durability and performance.",
            "features": [
                "Professional grade",
                "Durable materials",
                "Ergonomic design",
                "High performance",
                "Long lasting"
            ],
            "specifications": {
                "Model": f"SP-{product_id:04d}",
                "Brand": brands[random.choice([4, 11])],
                "Warranty": f"{random.choice([1, 2])} Year(s)",
                "Made In": random.choice(countries)
            },
            "variants": {
                "color": random.sample(["Black", "White", "Blue", "Red", "Green", "Yellow"], k=3),
                "size": ["S", "M", "L", "XL"] if "Shoes" in template['name'] or "Bag" in template['name'] else []
            },
            "icon": template['icon'],
            "new": random.random() > 0.7,
            "featured": random.random() > 0.85
        }
        product_id += 1
    
    # Category 5: Books (100 products)
    for i in range(100):
        template = books_products[i % len(books_products)]
        adj = random.choice(adjectives)
        qual = random.choice(qualities)
        name = f"{adj} {template['name']} {qual}"
        
        base_price = random.uniform(template['price_range'][0], template['price_range'][1])
        discount = random.choice([0, 0, 0, 5, 10, 15, 20])
        price = round(base_price * (1 - discount / 100), 2)
        
        products[str(product_id)] = {
            "id": product_id,
            "name": name,
            "slug": name.lower().replace(' ', '-') + f"-{product_id}",
            "category_id": 5,
            "brand_id": random.choice([5, 12]),
            "price": price,
            "original_price": round(base_price, 2),
            "discount_percent": discount,
            "rating": round(random.uniform(3.5, 5.0), 1),
            "reviews_count": random.randint(10, 500),
            "stock": random.randint(0, 100),
            "description": f"Engaging {template['name'].lower()} for readers",
            "long_description": f"Immerse yourself in this captivating {template['name'].lower()}. A must-read for book lovers.",
            "features": [
                "Engaging content",
                "Well written",
                "Quality binding",
                "Easy to read",
                "Great reviews"
            ],
            "specifications": {
                "Model": f"BK-{product_id:04d}",
                "Brand": brands[random.choice([5, 12])],
                "Warranty": "N/A",
                "Made In": random.choice(countries)
            },
            "variants": {
                "color": [],
                "size": ["Paperback", "Hardcover"]
            },
            "icon": template['icon'],
            "new": random.random() > 0.7,
            "featured": random.random() > 0.85
        }
        product_id += 1
    
    return products

# Generate and save products
products = generate_products()
with open('products.json', 'w', encoding='utf-8') as f:
    json.dump(products, f, indent=2, ensure_ascii=False)

print(f"Generated {len(products)} products successfully!")
