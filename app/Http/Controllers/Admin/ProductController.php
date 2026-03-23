<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->notArchived();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products   = $query->latest()->paginate(12);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:products',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'      => 'required|in:disponible,rupture,archive',
        ]);

        $validated['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            //has file vérifie si un fichier a été téléchargé pour le champ "image". Si c'est le cas, on stocke le fichier dans le dossier "products" du disque "public" et on enregistre le chemin dans la base de données.
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        if ($validated['stock'] == 0 && $validated['status'] === 'disponible') {
            $validated['status'] = 'rupture';
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Burger créé avec succès !');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:products,name,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'      => 'required|in:disponible,rupture,archive',
        ]);

        $validated['slug'] = Str::slug($request->name);
        //slug est une version "nettoyée" du nom du produit, 
        //sans espaces ni caractères spéciaux, pour être utilisée dans les URLs. 
        //Str::slug() est une fonction de Laravel qui génère automatiquement 
        //ce slug à partir du nom du produit.

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        if ($validated['stock'] == 0 && $validated['status'] === 'disponible') {
            $validated['status'] = 'rupture';
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Burger mis à jour avec succès !');
    }

    public function archive(Product $product)
    {
        $product->update(['status' => 'archive']);
        return back()->with('success', 'Burger archivé.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return redirect()->route('admin.products.index')
            ->with('success', 'Burger supprimé.');
    }
}